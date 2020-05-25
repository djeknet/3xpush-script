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

@$dev = intval($_GET['dev']);

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/info.php");
require_once('include/SxGeo.php');

include_once 'include/devicedetector/spyc/Spyc.php';
include_once 'include/devicedetector/autoload.php';

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

use DeviceDetector\DeviceDetector; 

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

$url = $_GET['p'];
$url = encodelink($url, 2);
parse_str($url, $arr);

$advs_id = intval($arr['advs_id']); // advs id
$myads_id = intval($arr['adv_id']); // myads id
$admin_id = intval($arr['owner']); // owner of subscriber
$sendid = intval($arr['sendid']); // send_report id
$uid = intval($arr['uid']); // subscribers id
$ip_view = text_filter($arr['ip']);
$country_view = text_filter($arr['cc']);
$sended = text_filter($arr['sended']);
$time_view = text_filter($arr['tv']);
$out = text_filter($arr['out']);
$bid = text_filter($arr['bid']);
$wm_money = text_filter($arr['wm']);
$sid = intval($arr['sid']);
$feed_id = intval($arr['feed_id']);
$subid = text_filter($arr['subid']);
$code = text_filter($arr['code']);
$browser_id = intval($arr['brid']);
$rand_link_number = intval($arr['rnd']);
$traf_exchange = intval($arr['te']); // 1 - traffic exchange
$adm_sid = intval($arr['adm_sid']); // site id for exchange
$days = intval($arr['d']); // days of subscription
$user_id = intval($_COOKIE['uid']);
$agent = getenv("HTTP_USER_AGENT");
$real_ip = getenv("REMOTE_ADDR");
$real_agent = getenv("HTTP_USER_AGENT");
$today = date("Y-m-d H:i:s");
$clicks_cookie = allcookie("c", "get");
$check_code = md5($config['global_secret'].$bid.$wm_money.$advs_id.$sid);

$all_clicks = $clicks_cookie['ids'];
if ($all_clicks) {
    $all_clicks = explode(',', $all_clicks);
} else $all_clicks = array();

$adid = $sid."-".$myads_id."-".$feed_id."-".$advs_id;

if (!in_array($adid, $all_clicks)) {
$clicks_cookie['ids'] = $clicks_cookie['ids'].",".$adid;    
allcookie("c", "set", $clicks_cookie);
}
if (!$admin_id) $admin_id=0;

$nopay=0;
$settings = settings("AND admin_id=".$admin_id."");
if (!$settings['send_afterclick']) $settings['send_afterclick'] = 4;

if (!$user_id) {
    $user_id = $rand_link_number;
    setcookie("uid", $user_id, time() + 86000 * 360);
}

$geoip = geoip_new($real_ip);

if ($country_view!=$geoip['cc']) {
$nopay=1;
$stop[] = "diff regions: ".$country_view."!=".$geoip['cc']."";     
}
if ($uid && $out) {

$deviceDetector = new DeviceDetector($agent);
$deviceDetector->parse();

$device = $deviceDetector->getDeviceName();
$brand = $deviceDetector->getBrandName();
$model = $deviceDetector->getModel();
$os = $deviceDetector->getOs();
$br = $deviceDetector->getClient();

if ($myads_id) {
$ads = myads("AND id=".$myads_id."");

if ($ads) {
$ads[$myads_id]['icon'] = str_replace("../", "", $ads[$myads_id]['icon']);
if ($ads[$myads_id]['image']) {
$ads[$myads_id]['image'] = str_replace("../", "", $ads[$myads_id]['image']);
$img = "https://".$settings['domain']."/".$ads[$myads_id]['image'];
} else $img = '';

$icon = "https://".$settings['domain']."/".$ads[$myads_id]['icon'];
}
}

$out = base64_decode($out);
$macroses = array(
'&amp;' => '&',
'[ID]' => $myads_id,
'[UID]' => $user_id,
'[SID]' => $sid,
'[SUBID]' => $subid,
'[BID]' => $bid,
'[ISO]' => $geoip['cc'],
'[COUNTRY]' => $geoip['country'],
'[REGION]' => $geoip['region'],
'[CITY]' => $geoip['city'],
'[AD_ICON]' => $icon,
'[AD_IMG]' => $img,
'[AD_TITLE]' => $ads[$myads_id]['title'],
'[AD_TEXT]' => $ads[$myads_id]['text'],
'[DEVICE]' => $device,
'[BRAND]' => $brand,
'[MODEL]' => $model,
'[OS]' => $os['name'],
'[BROWSER]' => $br['name'],
'[DAYS]' => $days,
);

foreach ($macroses as $key => $value) {
$out = str_replace($key, $value, $out);
}

if ($dev!=1) {
header("Location: $out");
fastcgi_finish_request();
}

if ($check_code!=$code) {
$nopay=1;
$stop[] = "check code error";    
}

// check if ip match when viewing and clicking
if ($ip_view && $ip_view!=$real_ip) {
//$nopay=1;
$stop[] = "ip view: $ip_view!=$real_ip";    
}

// check click on a random number to protect against fraud 
if ($memcached) {
$code_click = md5("clickrnd".$rand_link_number.$myads_id.$advs_id.$sid);
$rnd_is_click = $memcached->get($code_click);
if ($rnd_is_click) {
$nopay=1;
$stop[] = "clickid double click"; 
}
$memcached->set($code_click, $today, false, time() + 86000);

// cache for checking user click on ad
$code_click = md5("click".$real_ip.$agent.$adid);
$is_click = $memcached->get($code_click);
if ($is_click) {
$nopay=1;
$stop[] = "id double click"; 
}
$memcached->set($code_click, $today, false, time() + 86000);
}
if ($all_clicks) {
    if (in_array($adid, $all_clicks)) {
     $nopay=1;
     $stop[] = "id double click cookie";    
    }
}
// check how much time has passed since the link was shown, if more than the specified, then reject
if ($time_view) { 
	 $spend_time = time() - $time_view;
    if ($spend_time > 86000) {
     $nopay=1;
     $stop[] = "24 hours have passed after viewing<br />";
    }
} 
 


if (!$bid) $bid = 0;
if (!$wm_money) $wm_money = 0;
if ($nopay==1) {
    $bid=0;
    $wm_money=0;
    $nopay_upd = ", clicks_nopay=clicks_nopay+1";
}

list($ip) = $db->sql_fetchrow($db->sql_query("SELECT ip FROM subscribers WHERE id='$uid'"))  or $errors['subscribers'] = mysqli_error();
if ($real_ip!=$ip) {
$ip = $real_ip;
$upd=1;
}

$db->sql_query("UPDATE subscribers SET clicks=clicks+1, money=money+".$wm_money.", next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_afterclick']." HOUR) WHERE id='".$uid."'");
if ($sendid) $db->sql_query("UPDATE send_report SET clicks=clicks+1, click_time=now() WHERE id='".$sendid."'");

$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'clicks', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");
$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'money', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + ".$wm_money."");
                  
if (!$subid) $subid = 0;
$db->sql_query('INSERT INTO daystat (date, admin_id, sid, subid, clicks, money)
        VALUES (CURRENT_DATE(), "' . $admin_id . '", ' . $sid . ', "' . $subid . '", 1, "' . $wm_money . '")
         ON DUPLICATE KEY UPDATE
 clicks = clicks + 1, money = money + "'.$wm_money.'"') or $errors['daystat'] = mysqli_error();

if ($traf_exchange==1) {
 $db->sql_query('INSERT INTO traf_exchange_stat (id, date, site_id, admin_site, clicks) 
        VALUES (NULL, CURRENT_DATE(), ' . $sid . ', "' . $adm_sid . '", 1) 
         ON DUPLICATE KEY UPDATE 
 clicks = clicks + 1') or $errors['traf_exchange_stat'] = mysqli_error();   
}

if ($feed_id) {
$db->sql_query("INSERT INTO feed_stat (date, admin_id, feed_id, clicks, money, wm_money)
                  VALUES (CURRENT_DATE(), '1', " . $feed_id . ",  1, '".$bid."', '".$wm_money."')
                  ON DUPLICATE KEY UPDATE
                  clicks = clicks + 1, money = money + ".$bid.", wm_money = wm_money + ".$wm_money."") or $errors['feed_stat'] = mysqli_error();
} elseif ($myads_id) {
$db->sql_query("UPDATE myads SET clicks=clicks+1 WHERE id=" . $myads_id . "")  or $errors['myads'] = mysqli_error();
}

if ($advs_id) {
$db->sql_query("UPDATE advs SET clicks=clicks+1, money=money+".$wm_money." WHERE id='".$advs_id."'");

$db->sql_query("INSERT INTO advs_stat (id, date, advs_id, clicks, clicks_nopay, wm_money, money)
                  VALUES (NULL, CURRENT_DATE(), '" . $advs_id . "', 1,  ".$nopay.", '".$wm_money."', '".$bid."')
                  ON DUPLICATE KEY UPDATE
                  clicks = clicks + 1, money = money + ".$bid.", wm_money = wm_money + ".$wm_money." ".$nopay_upd."") or $errors['advs_stat'] = mysqli_error();
}                  



if ($upd==1 && $geoip['cc']) {
$db->sql_query("UPDATE subscribers SET ip='".$real_ip."', cc='".$geoip['cc']."', region='".$geoip['region']."',  city='".$geoip['city']."' WHERE id='".$uid."'") or $errors['subscribers2'] = "UPDATE subscribers SET ip='".$real_ip."', cc='".$geoip['cc']."', region='".$geoip['region']."',  city='".$geoip['city']."', ip_range='".$ip_range."' WHERE id='".$uid."'";
}


if ($wm_money>0) {
$check_code = fff($wm_money);
$db->sql_query("UPDATE balance SET summa=summa+".$wm_money.", allmoney=allmoney+".$wm_money.", last_edit=now(), last_sum='".$wm_money."', check_code='".$check_code."' WHERE admin_id=" . $admin_id . "")  or $errors['balance'] = "UPDATE balance SET summa=summa+".$wm_money.", allmoney=allmoney+".$wm_money.", last_edit=now(), last_sum='".$wm_money."', check_code='".$check_code."' WHERE admin_id=" . $admin_id . "";
}
       
$db->sql_query("INSERT INTO region_stat (date, admin_id, sid, cc, clicks, money)
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $sid . ",  '" . $geoip['cc'] . "', 1, '".$wm_money."')
                  ON DUPLICATE KEY UPDATE
                  clicks = clicks + 1, money = money + ".$wm_money."") or $errors['region_stat'] = mysqli_error();

$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, clicks, money)
                  VALUES (CURRENT_DATE(), '" . $browser_id . "', '" . $admin_id . "', '".$sid."', 1, '".$wm_money."')
                  ON DUPLICATE KEY UPDATE
                  clicks = clicks + 1, money = money + ".$wm_money."") or $errors['browser_stat'] = mysqli_error();

$minutes = DateDiffInterval( $sended, $today, 'M' );

if ($stop) $comment = json_encode($stop, JSON_UNESCAPED_UNICODE); else $comment='';

$db->sql_query("INSERT INTO clickstat (id, admin_id, createtime, date, time, subscriber_id, advs_id, sid, subid, money, ip, feed_id, minutes,  click_id, os, browser, cc, device, brand, model, comment, days)
        VALUES (NULL, '".$admin_id."', now(), CURRENT_DATE(), CURRENT_TIME(), ".$uid.", " . $advs_id . ", " . $sid . ", '".$subid."', '" . $wm_money . "', '".$ip."', '".$feed_id."', '".$minutes."', '".$rand_link_number."', '".$os['name']."', '".$br['name']."', '".$geoip['cc']."', '".$device."', '".$brand."', '".$model."', '".$comment."', '".$days."')")  or $errors['clickstat'] = mysqli_error();

// write the reasons for not paying a click                 
if (is_array($stop)) {
   foreach ($stop as $key => $value) {
   $db->sql_query("INSERT INTO sites_nopay (id, date, sid, reason, clicks)
                  VALUES (NULL, now(), '" . $sid . "', '".$value."', 1)
                  ON DUPLICATE KEY UPDATE
                  clicks = clicks + 1");
   }     
}

if ($settings['check_url']==1 && $out && $feed_id) {
$answer = check_http_status($url);
$good = array(200, 302);
if (!in_array($answer, $good)) {
$text = "feed $feed_id OFF, get url fail: $out";
alert($text, 1, 'warning');
if ($feed_id) $db->sql_query("UPDATE feeds SET status=0 WHERE id='".$feed_id."'");
}
}
// send error notifications for admin
if (is_array($errors)) {
    $errors = json_encode($errors);
  alert(1, $errors, 'danger');  
}

} else {
echo 'empty id';
}

if ($dev==1) {
echo "URL: $url <br>";
echo "OUT: $out <br>";
echo "Money: wm_money - $wm_money, bid - $bid <br>";
echo "clicks_cookie ($adid): <br>";
print_r($clicks_cookie);
echo '<br>stop: <br>';
print_r($stop);
echo '<br>errors:<br> ';
print_r($errors);
}