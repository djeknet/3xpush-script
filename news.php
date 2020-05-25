<?php 
// 3xpush Script - Push Subscription Management System 
// Copyright 2020 Evgeniy Orel
// Site: https://script.3xpush.com/
// Email: script@3xpush.com
// Telegram: @Evgenfalcon
//
// ======================================================================
// This file is part of 3xpush Script.
//
// 3xpush Script is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// 3xpush Script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with 3xpush Script.  If not, see <https://www.gnu.org/licenses/>.
//======================================================================

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/stat.php");
require_once("include/info.php");

@$dev = intval($_GET['dev']);

if ($dev==1) {
  $check_login = check_login(); 
  if ($check_login['root']!=1) $dev=0; 
}

if ($dev==1) {
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 1);
 } else {
error_reporting(0);
ini_set('display_errors', 0);
 }
 
if ($dev!=1) {
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
Header("Content-Encoding: none");
}
if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

$total_time = microtime(1);
$params['subs_id'] = intval($_GET['subs_id']); // id из subscribers
if (!$params['subs_id']) exit;
$ip = getenv("REMOTE_ADDR");

$subs_info = subscribers("AND id=".$params['subs_id']."");
if (!$subs_info) {
    if ($dev==1) echo "subscriber not found";
    exit;
}
                    
if ($config['memcache_ip']) {
$code = "request2".$params['subs_id'];
$num_request = $memcached->get($code);
if (!$num_request) $num_request=0;
if ($num_request >= 24) {
    if ($dev==1) {
        echo "More than 24 hits per hour with this ID [exit]<br>";
    } else {
$ads_view['noads'] = 1;
echo json_encode($ads_view, JSON_UNESCAPED_UNICODE);       
exit;
}
}

$num_request= $num_request + 1;
$memcached->set($code, $num_request, false, time() + 3600);
}

$subs_info = $subs_info[0];
// if the current ip is not equal to the saved one, then update it
if ($ip!=$subs_info['ip'] && $config['local_proj']!=1) {
    $subs_info['ip'] = $ip;
    $db->sql_query("UPDATE subscribers SET ip='".$ip."' WHERE id='".$subs_info['id']."'") or $errors[] = mysqli_error();

}

if ($subs_info['device']!=='desktop') $mobile=1; // if not a desktop, then it means a mobile device

// get the settings of the site of the owner of the subscriber to filter by stop words of advertising
$site_conf = sites("AND id=".$subs_info['sid']."");
$site_conf = $site_conf[$subs_info['sid']];

if ($site_conf['stopwords']) {
   $stopwords_arr = explode(",", $site_conf['stopwords']);
}
$geoip = geoip_new($ip);

$ads_view = array();
$settings = settings("AND admin_id=".$subs_info['admin_id']."");
$feeds = feeds("AND status=1 AND (max_send=0 OR total_sended < max_send)");

if (empty($feeds)) {
if ($dev==1) echo $subs_info['admin_id'].' - no active feeds or ads<br>';
exit;
}
$today = date("Y-m-d H:i:s");
$createtime = new Datetime($subs_info['createtime']);
$sub_date = $createtime->format('Y-m-d');
$datetime2 = date_create($today);
$datetime1 = date_create($subs_info['createtime']);
$interval = date_diff($datetime1, $datetime2, true);
$sub_days = $interval->days;
 
$i=0; 
$time_feeds[0] = microtime(1);       
 
$subs_info['sub_days'] = $sub_days;
$subs_info['sub_date'] = $sub_date;
 
$get_feed_ads = get_feed_ads($subs_info);

$time_feeds[1] = microtime(1); 
  
if (isset($get_feed_ads['ads'])) $ads = $get_feed_ads['ads']; else $ads = array();
if (isset($get_feed_ads['last_num'])) $i=$get_feed_ads['last_num']; 
 
if (is_array($get_feed_ads)) $get_feed_ads_count = count($get_feed_ads);

if ($dev==1) {

echo "subs_info:<br>";   
print_r($subs_info);
echo '<hr>';    
 echo "feeds info, ads received: ".$get_feed_ads_count."<br><pre>";   
print_r($get_feed_ads);
echo '</pre><hr>';
}  
                
if (empty($ads)) {

$settings['send_every'] = 24; // if there is no advertising, then we’ll do the next newsletter in a day

$db->sql_query("UPDATE subscribers SET `empty`=`empty`+1, next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR) WHERE id='".$subs_info['id']."'") or $errors[] = mysqli_error();

$db->sql_query("INSERT INTO daystat (date, admin_id,  sid, subid, empty_send)
                      VALUES (CURRENT_DATE(), ".$subs_info['admin_id'].", " . $subs_info['sid'] . ", '" . $subs_info['subid'] . "', '1')
                       ON DUPLICATE KEY UPDATE
                      empty_send = empty_send + 1") or $errors[] = mysqli_error();

$db->sql_query("INSERT INTO region_stat (date, admin_id,  sid, cc, `empty`)
                  VALUES (CURRENT_DATE(), ".$subs_info['admin_id'].", " . $subs_info['sid'] . ", '" . $geoip['cc'] . "',  1)
                  ON DUPLICATE KEY UPDATE
                  `empty` = `empty` + 1") or $errors[] = mysqli_error();
                  
$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, empty_send)
                  VALUES (CURRENT_DATE(), '" . $subs_info['browser_id'] . "',  ".$subs_info['admin_id'].", " . $subs_info['sid'] . ",  1)
                  ON DUPLICATE KEY UPDATE
                  empty_send = empty_send + 1") or $errors[] = mysqli_error();   
    if ($dev==1) {
         echo "<br />No feed ads<br />";
         print_r($errors);
    }                  
exit;
}
// sort ads list by sort param, depend on system settings                    
$ads = multiSort($ads, 'sort', SORT_DESC, SORT_REGULAR);

if ($dev==1) {
    echo "ads:<br>";   
    print_r($ads);
    echo '<hr>';
}
$subs_history = subs_history($subs_info);
if ($dev==1) {
    echo "subs_history: ";
    print_r($subs_history);
}
if (!$subs_history) {$subs_history = array(); $subs_history['hashes']  = array(); }

                $send=0;
                $adv_uniq=0;
                $stat_sended=array();
                
                foreach  ($ads as $num => $values) {
                if (empty($values['title'])) continue;
                $adv_hash = sha1($values['admin_id'].$values['feed_id'].$values['title'].$values['body']);
                  
                  if (!in_array($adv_hash, $subs_history['hashes'])) {
                   if ($dev==1) {
                        echo "$num. $adv_hash OK!<br>";
                    }
                  $adv_uniq=1;
                  $send=1;
             	  break;
                  } else {
                    
                   $stat_sended[$values['title']]++;
                  }
                }

                $winurl = $ads[$num]['winurl'];
                $feed_id = $ads[$num]['feed_id'];
                $bid = $ads[$num]['bid'];
                $adv_title = $ads[$num]['title'];
                $adv_opis = $ads[$num]['body'];
                $target_url = $ads[$num]['link'];
                
                   // if the bid per click is indicated, and these are ads from the feed, then we calculate the webmaster’s earnings with a minus commission for feeds
                    if ($bid > 0) {           
                   $comission = round($bid / 100 * $settings['feeds_proc'], 4);
                   $wm_money = round($bid - $comission, 3);
                   }

                
             if ($send!=1) {
                $settings['send_every'] = 24; // if there is no advertising, then we’ll do the next newsletter in a day
                if ($dev==1)  {echo "empty_ads<br />"; print_r($stat_sended);}
                $db->sql_query("UPDATE subscribers SET `empty`=`empty`+1, next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR) WHERE id='".$subs_info['id']."'");
                $stat['empty_ads']++;
                $ads_view['noads'] = 1;
                echo json_encode($ads_view, JSON_UNESCAPED_UNICODE);
                
                exit;
               }

                $db->sql_query("INSERT INTO advs (id, admin_id, feed_id, hash, icon, title, description, image, sended, uniq_sended, url)
                VALUES (NULL, '".$subs_info['admin_id']."',  '$feed_id', '$adv_hash', '".$ads[$num]['icon']."', '".addslashes($adv_title)."', '".addslashes($adv_opis)."', '".$ads[$num]['image']."', 1, 1, '".$target_url."')
                ON DUPLICATE KEY UPDATE sended = sended + 1, uniq_sended = uniq_sended + ".$adv_uniq.", update_date=now()");

                $advs_id = $db->sql_nextid();
                list($os_id) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  os WHERE `key` = '".$subs_info['os']."'"));

                if ($bid) {
                    $update_bid = ", cpc_money=cpc_money+".$bid."";
                }
         
                $db->sql_query("INSERT INTO advs_targets (id, advs_id, date, region, os_id, browser_id, lang, devtype, device, ip_range, views, cpc_money)
                VALUES (NULL, '".$advs_id."',  now(), '".$geoip['cc']."', '".$os_id."', '".$subs_info['browser_id']."', '".$subs_info['lang']."', '".$subs_info['device']."',  '".$subs_info['brand']."', '".$subs_info['ip_range']."', 1, '".$bid."')
                ON DUPLICATE KEY UPDATE views = views + 1 ".$update_bid."") or $stat['FAIL_advs_targets']++;
                
                
                $rand =  rand(10,9999999999);    
                
                $min_price=0;
                $max_price=0;
                $min_ctr=0;
                $max_ctr=0;
                $bids = $get_feed_ads['bids'];
                if (!empty($bids)) {
                	$min_price = min($bids);
                	$max_price = max($bids);
                }
                
                if (!empty($ctr_list)) {
                	$min_ctr = min($ctr_list);
                	$max_ctr = max($ctr_list);
                }
                $ctr_list=array();

                    
                    $ctr =   $ads[$num]['ctr'];
                    if (!$ctr) $ctr=0;
                    $isprice = $ads[$num]['isprice'];
                    if (!$isprice) $isprice=0;
                    if ($isprice) $comment .= "isprice: $isprice. ";
                    //$comment .= "NEWS";

                  

                    $position = $num;
                    if ($position==0) $position=1;
                    $startTime = new Datetime($subs_info['last_send']);
                    $hours = round(DateDiffInterval($subs_info['last_send'], $today), 0);
                      
                    $db->sql_query("INSERT INTO send_report (view_time, admin_id, min_price, max_price, min_ctr, max_ctr, all_advs, hours, comment, ctr, position, `createtime`, id, feed_hash, sid, subscriber_id, subid, adv_id, feed_adv_id, money, feed_id, tag)
                    VALUES (now(), '".$subs_info['admin_id']."', '".$min_price."', '".$max_price."', '".$min_ctr."', '".$max_ctr."', '".$i."', '".$hours."', '".$comment."', '".$ctr."', '".$position."', now(), NULL, '".$adv_hash."', '".$subs_info['sid']."', '".$subs_info['id']."', '".$subs_info['subid']."', '".$advs_id."', '".$ads[$num]['id']."', '".$wm_money."', '".$feed_id."', '".$subs_info['tag']."')");//  or die ("<center><br>".mysqli_error()."</center>");
                    
                    $message_id = $db->sql_nextid();
                    
                    $db->sql_query("UPDATE subscribers SET last_send_id='".$message_id."', last_hash_adv='".$adv_hash."' WHERE id='".$subs_info['id']."'");
                    
                 $title_len = mb_strlen($ads[$num]['title']);
                $body_len=0;
                if (!empty($ads[$num]['body'])) {
                $body_len = mb_strlen($ads[$num]['body']);
                 }
                 
                    
                      if ($title_len>0) {
                 $ads[$num]['title'] = str_replace("[COUNTRY]", $geoip['country'], $ads[$num]['title']);   
                 $ads[$num]['title'] = str_replace("[REGION]", $geoip['region'], $ads[$num]['title']);        
                 $ads[$num]['title'] = str_replace("[CITY]", $geoip['city'], $ads[$num]['title']);
                 $ads[$num]['title'] = str_replace("[BRAND]", $subs_info['brand'], $ads[$num]['title']);
                 $ads[$num]['title'] = str_replace("[MODEL]", $subs_info['model'], $ads[$num]['title']);
                 $ads[$num]['title'] = str_replace("[OS]", $subs_info['os'], $ads[$num]['title']);
                 $ads[$num]['title'] = str_replace("[BROWSER]", $subs_info['browser_short'], $ads[$num]['title']);
                   }
                 if ($body_len>0) {
                 $ads[$num]['body'] = str_replace("[COUNTRY]", $geoip['country'], $ads[$num]['body']); 
                 $ads[$num]['body'] = str_replace("[REGION]", $geoip['region'], $ads[$num]['body']);     
                 $ads[$num]['body'] = str_replace("[CITY]", $geoip['city'], $ads[$num]['body']);
                 $ads[$num]['body'] = str_replace("[BRAND]", $subs_info['brand'], $ads[$num]['body']);
                 $ads[$num]['body'] = str_replace("[MODEL]", $subs_info['model'], $ads[$num]['body']);
                 $ads[$num]['body'] = str_replace("[OS]", $subs_info['os'], $ads[$num]['body']);
                 $ads[$num]['body'] = str_replace("[BROWSER]", $subs_info['browser_short'], $ads[$num]['body']);
                  }
                  
                   if ($title_len>0 && $body_len>0) {
                 if ($title_len>$body_len) {
                 $ads_view['title'] = $ads[$num]['body'];
                 $ads_view['body'] = $ads[$num]['title'];
                 } else {
                 $ads_view['title'] = $ads[$num]['title'];
                 $ads_view['body'] = $ads[$num]['body'];
                 }
                 } elseif ($ads[$num]['title'] && !$ads[$num]['body']) {
                 $ads_view['title'] = $ads[$num]['title'];
                 $ads_view['body'] = $ads[$num]['title'];
                 } elseif (!$ads[$num]['title'] && $ads[$num]['body']) {
                 $ads_view['title'] = $ads[$num]['body'];
                 $ads_view['body'] = $ads[$num]['body'];
                 }

                if (mb_strlen($ads_view['title'])>$settings['trunk_title']) {
                    $ads_view['title'] = mb_substr($ads_view['title'], 0, $settings['trunk_title']).'...';
                }

                if (mb_strlen($ads_view['body'])>$settings['trunk_description']) {
                  $ads_view['body'] = mb_substr($ads_view['body'], 0, $settings['trunk_description']).'...';
                }

                $ads_view['icon'] = $ads[$num]['icon'];

                if ($ads[$num]['image'] && $settings['image_send']==1){
                $ads_view['image'] = $ads[$num]['image'];
                }
                $code = md5($config['global_secret'].$bid.$wm_money.$advs_id.$subs_info['sid']);
                // go link
                $link = "owner=".$subs_info['admin_id']."&cc=".$geoip['cc']."&sendid=".$message_id."&uid=".$subs_info['id']."&ip=".$ip."&hash=".$adv_hash."&sended=".$today."&tv=".time()."&brid=".$subs_info['browser_id']."&rnd=".$rand."&adv_id=".$ads[$num]['id']."&advs_id=".$advs_id."&wm=".$wm_money."&bid=".$bid."&sid=".$subs_info['sid']."&feed_id=".$feed_id."&code=".$code."&out=".base64_encode($target_url)."&subid=".$subs_info['subid']."&d=".$sub_days."";
                $urlparams = encodelink($link, 1);
                    
                $ads_view['link'] = 'https://'.$settings['domain'].'/go.php?p='.$urlparams;
                 
                $ads_view['subs_id'] = $subs_info['id'];
                $ads_view['siteid'] = $subs_info['sid'];
                $ads_view['advid'] = $ads[$num]['id'];
                $ads_view['advsid'] = $advs_id;
                $ads_view['subid'] = $subs_info['subid'];
                if ($ads[$num]['button1'] || $ads[$num]['button2']) {
                $ads_view['actions'] = "[{\"action\": \"explore\", \"title\": \"".$ads[$num]['button1']."\"},{\"action\": \"close\", \"title\": \"".$ads[$num]['button2']."\"}]";  
                }
                                                                                       
                           
                   
if ($dev==1)  {
if ($subs_history['hashes']) $history_count = count($subs_history['hashes']);    
echo "<b>all advs</b>: $i <br />";
echo "<b>send (feed: ".$feed_id.", ad id: ".$ads[$num]['id'].", advs_id: ".$advs_id.", coef: ".$ads[$num]['coef'].", num: $num): </b> ".$ads[$num]['title']." - ".$ads[$num]['body']." (bid: ".$ads[$num]['bid'].", wm money: ".$wm_money.", usd: ".$ads[$num]['usd'].", isprice: ".$ads[$num]['isprice']."), winurl: ".$winurl."<br /><br />";
echo "<strong>history_count</strong>: ".$history_count."<br />";
echo "<strong>target_url</strong>: $target_url<br />";
echo "<br /><strong>link</strong>: $link<br />";
echo "<br><strong>bids</strong> (min price: ".$min_price.", max price: ".$max_price.")<br>:";
print_r($bids);
echo '<hr><strong>SEND</strong>:<br>';
}

echo json_encode($ads_view, JSON_UNESCAPED_UNICODE);
if ($dev!=1) {
fastcgi_finish_request();    
}
            
                  $db->sql_query("INSERT INTO feed_stat (date, admin_id, feed_id, sended)
                  VALUES (CURRENT_DATE(), 1, " . $feed_id . ", 1)
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1");
                  
                  $db->sql_query("UPDATE feeds SET total_sended=total_sended+1 WHERE id='".$feed_id."'"); 
                  
           // write prices from feeds
           if (isset($get_feed_ads['ads'])) {
      
             foreach ($get_feed_ads['ads'] as $key => $value) {
               
               $db->sql_query('INSERT INTO feed_region_prices (date, feed_id, cc, mob, money, requests)
        VALUES (CURRENT_DATE(),  "' . $value['feed_id'] . '", "' . $subs_info['cc'] . '", "' . $mobile . '", "' . $value['isprice_usd'] . '", 1)
         ON DUPLICATE KEY UPDATE money=money+' . $value['isprice_usd'] . ', requests=requests+1'); 
         
             }
            
           }
         // if winurl is specified, then get it 
         if ($winurl) {
            $params = array(
    'action' => 'send',
    'ip' => ''.$subs_info['ip'].'',
    'uid' => ''.$subs_info['id'].'',
);
$result = file_get_contents($winurl, false, stream_context_create(array(
    'http' => array(
        'timeout' => 1,
        'method'  => 'GET',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($params)
    )
)));
           }
           
$total_time_end = microtime(1); 
if ($dev==1)  {
$t[0] =  round($total_time_end - $total_time, 5);
$t[1] = round($time_feeds[1] - $time_feeds[0], 5);

echo "<br><br>time: ".$t[0]."<br>
get feeds: ".$t[1]."<br>
";   
    }