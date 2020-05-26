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

// injection filter
function sql_filter($data) {
     $code_match = array("/", "'", '`', '&#039', '&#034', '"', '&#042', '&#043', '&#148', '&quot','()','(0)', 'sysdate');

     $data = str_replace($code_match, "", $data);
     return $data;
    }
    
    
// function of checking incoming data in the array, as well as checking the security of the request
function check_input($data, $security=0) {
$warn=0;
$stop_words = array('select', 'from', 'sleep', 'sysdate');  
$code_match = array('"', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', '"', '<', '>', '?', ';', "'", ',', '/', '', '~', '`', '=');

      if (is_array($data)) {
        
         foreach ($data as $key => $value) {
              if (is_array($value)) {
                     foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                                foreach ($value2 as $key3 => $value3) {
                                        $value3_is = $value3; 
                        $value3 = str_replace($code_match, '', $value3);
                        $value3 = text_filter($value3);
                        $data[$key][$key2] = $value3;
                        
                                if (is_array($stop_words) && $security==1) { 
                                foreach ($stop_words as $num => $word) {
                                 if ($word && stripos($value3_is, $word) != false) {
                                $warn++; 
                                 }
                                 }
                                    }
                                    }
                            
                            } else {
                     
                        $value2_is = $value2; 
                        $value2 = str_replace($code_match, '', $value2);
                        $value2 = text_filter($value2);
                        $data[$key][$key2] = $value2;
                        
                      if (is_array($stop_words) && $security==1) { 
                             foreach ($stop_words as $num => $word) {
                              if ($word && stripos($value2_is, $word) != false) {
                                $warn++; 
                              }
                             }
                            }
                            }
                        }
                } else {
$value_is = $value; 
$value = str_replace($code_match, '', $value);
$value = text_filter($value);
$data[$key] = $value;

if (is_array($stop_words) && $security==1) { 
                             foreach ($stop_words as $num => $word) {
                              if ($word && stripos($value_is, $word) != false) {
                                $warn++; 
                              }
                             }
                            }
                            
}
         }
      } else {
$data = str_replace($code_match, '', $data);
$data = text_filter($data);    
      }
      if ($security==1) { 
         if ($warn >= 2) {
            return 1;
         } else {
            return 0;
         }
      } else {
      return $data;
      }
    }
// check ip for blocking
function check_ip($ip='') {
    global $settings;
    
if (!$ip) $ip = getenv("REMOTE_ADDR");
$blocked_ip = 0;
if (!$settings) $settings = settings();
    
if ($settings['black_ip']) {
   $settings['black_ip'] = explode(",", $settings['black_ip']); 
   if (in_array($ip, $settings['black_ip'])) {
    $blocked_ip=1;
   }
}

return $blocked_ip;
 }
// save array in cookies
function allcookie($name, $type, $data='', $domain='') {
	if ($type=="get") {
	if(isset($_COOKIE[''.$name.'']))  {
$i = base64_decode($_COOKIE[''.$name.'']);
$i = json_decode($i, true);
} else $i = array();

return $i;
	} elseif ($data) {
$encoded = base64_encode(json_encode($data));
if ($domain) {
setcookie($name, $encoded, time() + 86000 * 90, '/', $domain);
} else {
setcookie($name, $encoded, time() + 86000 * 90);
}
	}
}
// get today date
function today($type=0)
{
   if (!$type) {
    return date ("Y-m-d");
   } 
}

//check Google Recaptcha responcse
function recaptcha() {

$recaptcha=$_POST['g-recaptcha-response'];
$settings = settings();

    if(!empty($recaptcha))
    {
        $google_url="https://www.google.com/recaptcha/api/siteverify";
   
        $ip=$_SERVER['REMOTE_ADDR'];
        $url=$google_url."?secret=".$settings['google_recaptcha']."&response=".$recaptcha."&remoteip=".$ip;
        
        $res=get_curl($url);
        $res= json_decode($res, true);
     //var_dump($res);
        if($res['success'])
        {
           return 1;
        }
        else
        {
          return 0;
        }
 
    }
    else
    {
           return 0;
    }
 
}
// curl function for recatcha
function get_curl($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36");
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}

// checking links for moderation
function check_moderate($url) {
     global $db;
     
     $urls = parse_url($url);
     
     $is_moderate = get_onerow('id', 'myads', "url LIKE '%".$urls['host']."%' AND moderate=1 AND status=1");
     if ($is_moderate) {
        return 1;
     } else {
        return 0;
     }
    }
    
// generate links for landing ages   
function land_links($site_id) {
    
$site_settings = get_onerow('iframe_options', 'sites', "id=".$site_id."");  
$iframe_options=array();
$iframe_options['noredirect']=1;
$iframe_options['repeat']=0;
if ($site_settings) {
 $iframe_options = json_decode($site_settings, true);
}
if (!$iframe_options['url']) $iframe_options['url'] = 'URL';
if ($iframe_options['nogettext']==0) $text_link = $iframe_options['text'];
if ($iframe_options['nogeturl']==0) $url1 = "&url=".$iframe_options['url'];
                                          
$info['landing'] = "/land.php?id=".$site_id."&subid=&tag=&price=0&url=";
$info['iframe'] = "/frame.php?sid=".$site_id."&subid=&tag=&price=0&noredirect=".$iframe_options['noredirect']."&r=".$iframe_options['repeat']."&text=".$text_link."#".$iframe_options['url'];
$info['link'] = "/link.php?sid=".$site_id."&subid=&tag=&price=0&text=".$text_link."".$url1;

return $info;

    }
// loading image for sending
function load_img($admin_id=0) {
    global $_FILES;
    if ($_FILES) {
    $imgtype = "jpg,jpeg,png";
    if ($admin_id) {
    $directory = "../pushimg/".$admin_id;      
    } else {
    $directory = "../pushimg";    
    }
    
        if (!is_dir($directory)) {
            mkdir($directory, 0777);
            $fp = fopen ("$directory/index.html", "w");
            fwrite($fp,'access dinied');
            fclose($fp);
        }


        foreach ($_FILES as $key => $val) {
            if ($val['size']==0) continue;
            $filename = upload($directory, $imgtype, 800000, "", 2000, 2000, $key);
            if ($stop) {
                return $stop;
                break;
                }
            $img = "".$directory."/".$filename."";
            if ($key=='img') {
                resize($img, 192, 192, 1, 0);
                $data['icon'] = $img;
            } else {
                resize($img, 360, 240, 1, 0);
                $data['image'] = $img;
            }
        }
        
        return $data;
        } else {
        return false;
        }
    }
// get users subnet repeats
function subnet($ip, $depth=2) {
    
    $net = explode(".", $ip);
    
    if ($depth==1) {
    $network = $net[0].".".$net[1];
    } elseif ($depth==2) {
    $network = $net[0].".".$net[1].".".$net[2];
    }
    
    return $network;
    }

// get ads list from feeds
function get_feed_ads($subs_info, $where='')
{
    global $db, $settings, $config;
    
    if (!$settings) $settings = settings("AND admin_id=".$subs_info['admin_id']."");
    $max_time = $settings['feed_timeout'];

    $feeds = feeds("AND (max_send=0 OR total_sended < max_send) AND timeout_next < now() ".$where);
    
if ($settings['sorttype'] == 2 || $settings['sorttype'] == 3 || $settings['block_unsubs'] == 1) {    
$advs_info = advs_info();
} else {
$advs_info = array();
}
$adv_ctr = $advs_info['adv_ctr'];
$blocked = $advs_info['blocked'];

    if (!$feeds) {
        
        return $data['info'] = 'no active feeds';
        exit;
    }
                $replaces = array(
                    'SITE_ID'   => $subs_info['sid'],
                    'IP'        => $subs_info['ip'],
                    'TOKEN'       => $subs_info['token'],
                    'SUB'       => $subs_info['subid'],
                    'LANG'       => $subs_info['lang'],
                    'DEVICE'       => $subs_info['device'],
                    'BRAND'       => $subs_info['brand'],
                    'MODEL'       => $subs_info['model'],
                    'REF'       => $subs_info['referer'],
                    'UID'       => $subs_info['id'],
                    'DATE'       => $subs_info['sub_date'],
                    'DAYS'       => $subs_info['sub_days'],
                    'AGENT'     => urlencode($subs_info['browser'])
                );
                
  if ($settings['stopwords']) {
	$stopwords_arr = explode(",", $settings['stopwords']);
	}
     $i=0; 
     $bids = array();            
    foreach ($feeds as $key => $value)
    {
        $total_time = microtime(1);
        if (!$value['url'])
            continue;
        if ($value['status'] != 1)
        {
                $info[] = "feed $key disabled<br />";
            continue;
        }
        $geo = $value['regions'];

        if ($geo)
        {
            $geos = explode(",", $geo);
            if (!in_array($cc, $geos))
            {
                $stat['nogeo']++;
                    $info[] =  "feed $key nogeo: $cc - $geo<br />";
                continue;
            }
        }
        $value['url'] = str_replace("&amp;", "&", $value['url']);
        $feed_url_get = str_replace(array_keys($replaces), array_values($replaces), $value['url']);

        $curl_result = get_fcontent($feed_url_get, 0, 1, $subs_info['lang']);
        $method=0;

        if ($curl_result[1]['http_code'] == 200)
        {
            $feed_data = json_decode($curl_result[0], true);
            if (empty($feed_data))
            {
                $method=1;
                @$feed_data = json_decode(file_get_contents($feed_url_get), true);
            }

        } elseif ($curl_result[1]['http_code'] == 204)
        {
             $stat['noads']++;
             $info[] =  "feed $key  code 204<br />";
              continue; 
            
        } elseif ($curl_result[1]['http_code'] == 400)
        {
             $method=2;
            @$feed_data = json_decode(file_get_contents($feed_url_get), true);
        }

        if (!empty($feed_data['data']))
        {
             $method=3;
            $feed_data = $feed_data['data'];
        }

        if (empty($feed_data[0]))
        {
             $method=4;
            $feed_data = array($feed_data);
        }
            $total_time_end = microtime(1);
            $time = round($total_time_end - $total_time, 5);
            
            if ($max_time > 0 && $max_time < $time) {
              $text = "Feed № $key timeout: $time sek, next request after 1 hour";
              $db->sql_query("UPDATE feeds SET timeout_next=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id='".$key."'");
              alert($text, 1, 'warning');
              $info[] = $text;
            } 
        if (!empty($feed_data))
        {

            $feed_count = count($feed_data);
            $info[] = "feed $key [".$value['name']."] received $feed_count ads (m ".$method.", time ".$time.") - ";

            $title_name = $value['feed_title'];
            $body_name = $value['feed_body'];
            $bid_name = $value['feed_bid'];
            $link_name = $value['feed_link_click_action'];
            $icon_name = $value['feed_link_icon'];
            $image_name = $value['feed_link_image'];
            $convert_rate = $value['convert_rate'];
            $feed_winurl = $value['feed_winurl'];
            $coef = $value['coef'];
            $feed_results = 0;

            foreach ($feed_data as $num => $val)
            {

                if (!$val[$title_name]) {
                 $info[] = "feed $key, no title by key $title_name<br>";
                 continue;    
                }
                
                    if (stripos($val[$title_name], "????") !== false)
                    {
                        $stat['wrong_title']++;
                            $info[] = "feed $key, error in title: " . $val[$title_name] . "<br>";
                        continue;
                    }
                    $stop_word = 0;
                    if (isset($stopwords_arr))
                    {
                        foreach ($stopwords_arr as $word)
                        {
                            if ($word && stripos($val[$title_name], $word) != false)
                            {
                                $stop_word = 1;
                                break;
                            }
                        }
                    }
                    if ($stop_word == 1)
                    {
                        $stat['stopwords']++;
                            $info[] = "feed $key, the presence of stop words in the title: " . $word . "<br>";
                        continue;
                    }
                    $isprice = $val[$bid_name];
                    

                    if ($convert_rate)
                    {
                        if (stripos($convert_rate, "/") !== false)
                        {
                            $convert_rates = str_replace("/", "", $convert_rate);
                            $val[$bid_name] = $val[$bid_name] / $convert_rates;
                        } else
                        {
                            $val[$bid_name] = $val[$bid_name] * $convert_rate;
                        }
                    }
                    $isprice_usd = $val[$bid_name];

                    if ($coef)
                    {
                        $minus = round($val[$bid_name] / 100 * $coef, 2);
                        $val[$bid_name] = $val[$bid_name] - $minus;
                    }

                    $hash = sha1($subs_info['admin_id'] . $key . $val[$title_name] . $val[$body_name]);

                    $sort = $val[$bid_name];
                    if (isset($adv_ctr[$hash]))
                    {
                        $ctr_list[] = $adv_ctr[$hash];

                        if ($settings['sorttype'] == 2)
                        {
                            $sort = $adv_ctr[$hash];
                        } elseif ($settings['sorttype'] == 3)
                        {
                            $ctr = $adv_ctr[$hash];
                            if (!$ctr)
                                $ctr = 0.01;
                            $sort = $sort * $ctr;
                        }
                    } else
                    {
                        $adv_ctr[$hash] = 0;
                    }

                    if ($blocked && in_array($hash, $blocked))
                    {
                        $stat['blocked']++;
                            $info[] = "feed $key, hash $hash blocked<br />";
                        continue;
                    }

                    if ($val[$bid_name] < $settings['minstavka'])
                    {
                        $stat['min_price']++;
                            $info[] = "feed $key, min price: " . $val[$bid_name] . " < " . $settings['minstavka'] . "<br />";
                        continue;
                    }
                    if ($val[$bid_name] > 10)
                    {
                        $stat['max_price']++;
                            $info[] = "feed $key, max price: " . $val[$bid_name] . " > 10<br />";
                        continue;
                    }
                    if (!$val[$icon_name])
                    {
                        $stat['feed_no_icon']++;
                            $info[] = "feed $key, no icon: " . $val[$icon_name] . "<br />";
                        continue;
                    }

                    
                    if (!isset($val[$feed_winurl]))
                        $val[$feed_winurl] = '';
                        $val[$bid_name] = round($val[$bid_name], 3);

                    $bids[] = $val[$bid_name];
                    $ads[$i]['title'] = $val[$title_name];
                    $ads[$i]['body'] = $val[$body_name];
                    $ads[$i]['bid'] = $val[$bid_name];
                    $ads[$i]['isprice'] = $isprice;
                    $ads[$i]['isprice_usd'] = $isprice_usd;
                    $ads[$i]['sort'] = $sort;
                    $ads[$i]['link'] = $val[$link_name];
                    $ads[$i]['icon'] = $val[$icon_name];
                    $ads[$i]['image'] = $val[$image_name];
                    $ads[$i]['id'] = $val['id'];
                    $ads[$i]['feed_id'] = $key;
                    $ads[$i]['coef'] = $coef;
                    $ads[$i]['convert_rate'] = $convert_rate;
                    $ads[$i]['winurl'] = $val[$feed_winurl];
                    $ads[$i]['ctr'] = $adv_ctr[$hash];
                    $ads[$i]['admin'] = 1;
                    $ads[$i]['button1'] = $val[$value['feed_button1']];
                    $ads[$i]['button2'] = $val[$value['feed_button2']];
                    $i++;
                    $feed_results++;
   
            }
        
            
             $db->sql_query("INSERT INTO feed_stat (date, admin_id, feed_id,  alltime, requests)
                  VALUES (CURRENT_DATE(), 1,  " . $key .",  '" . $time ."', 1)
                  ON DUPLICATE KEY UPDATE
                   requests = requests + 1, alltime=alltime+" . $time ."");
                   
            if ($feed_results > 0)
                 $info[] =  "feed $key results: $feed_results<br />";
        } else
        {
            $db->sql_query("INSERT INTO feed_stat (date, admin_id, feed_id, requests, `empty`, alltime)
                  VALUES (CURRENT_DATE(), 1, " . $key .",  1, 1, '" . $time ."')
                  ON DUPLICATE KEY UPDATE
                   `empty` = `empty` + 1, requests=requests+1, alltime=alltime+" . $time ."");

                 $info[] =  "feed $key empty, method  $method<br />";
        }
        
           
    }
    $data['ads'] = $ads;
    $data['bids'] = $bids;
    $data['info'] = $info;
    $data['last_num'] = $i;
    return $data;

}
// send push
function send_push($ads, $site, $token, $send = 1)
{
    $settings = settings();
    if ($site['server_key'])
        $server_key = '' . $site['server_key'] . '';
    else
        $server_key = '' . $settings['server_key'] . '';
    if (!$settings['time_to_live'])
        $settings['time_to_live'] = 30;

    $params = new stdClass();

    $params->notification = new stdClass();
    $params->data = new stdClass();

    $params->notification->title = $ads['title'];
    $params->notification->body = $ads['body'];
    $params->notification->icon = $ads['icon'];
    $params->notification->requireInteraction = "true";
    $params->notification->click_action = $ads['url'];
    $params->data->link = $ads['url'];
    $params->data->image = $ads['image'];
    $params->data->siteid = $site['sid'];
    $params->data->subid = $site['subid'];
    $params->data->advid = $ads['advid']; // id из myads и feed
    $params->data->advsid = $ads['advsid']; // id из advs
    $params->data->cpv = $ads['cpv'];
    $params->data->subs_id = $site['subs_id'];
    $params->data->subs_id2 = $site['subs_id2'];
    $params->data->requireInteraction = "true";
    $params->time_to_live = $settings['time_to_live'] * 60;
    $params->to = $token;

    if ($ads['options'])
    {
        //$params->data->vibrate = "[200, 100, 200, 100, 200, 100, 200]";
        //$params->data->silent =  "true";
        //$params->data->tag =  "renotify";
        //$params->data->renotify =  "true";
        $params->data->actions = "[{\"action\": \"explore\", \"title\": \"" . $ads['options']['button1'] .
            "\"},{\"action\": \"close\", \"title\": \"" . $ads['options']['button2'] . "\"}]";
    }


    $opts = ["http" => ["method" => "POST", "header" => "Authorization: key={$server_key}\r\n" .
        "Content-Type: application/json\r\n", "content" => json_encode($params)]];

    $context = stream_context_create($opts);

    // Open the file using the HTTP headers set above
    if ($send == 1)
    {
        $data = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
        if ($site['params'] == 1)
        {
            print_r($params);
        }
        if ($data)
        {
            return $data;
        } else
        {
            return false;
        }
    } else
    {
        return false;
    }
}
// ip2Location geobaze
function geoip_new($ip)
{
    global $root;
    require_once $root . '/include/ip2l/IP2Location.php';

    $db = new \IP2Location\Database($root .
        '/include/ip2l/databases/IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP.BIN', \IP2Location\Database::
        FILE_IO);
    $records = $db->lookup($ip, \IP2Location\Database::ALL);

    $geodata['cc'] = $records['countryCode'];
    $geodata['timezone'] = $records['timeZone'];
    $geodata['country'] = $records['countryName'];
    $geodata['region'] = $records['regionName'];
    $geodata['city'] = $records['cityName'];
    $geodata['isp'] = $records['isp'];

    return $geodata;
}
// push sending show short text
function short_text($text, $num)
{
    if (mb_strlen($text, "UTF-8") > $num)
    {
        $text = mb_substr($text, 0, $num);
        $text .= "... <i class=\"fa fa-angle-double-right\"></i>";
    }

    return $text;
}
// save hash to prevent reoperation
function sethash($hash)
{
    global $db;

    $db->sql_query("INSERT INTO alerts_hash (date, hash) VALUES
        (now(), '$hash') ON DUPLICATE KEY UPDATE date = now()");

    return true;
}

// format for big integers
function bigint($summ)
{
    $summ = number_format($summ, 0, ',', ' ');
    return $summ;
}
// format for money data
function moneyformat($summ)
{
    if (is_int($summ)) {
    $summ = number_format($summ, 4, ',', ' ');
    }
    return $summ;
    
}
// save data to journal
function jset($admin_id, $action = '', $error = 0)
{
    global $db;

    $ip = getenv("REMOTE_ADDR");
    $agent = getenv("HTTP_USER_AGENT");
    $page = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
    $ip_arr = geoip_new($ip);

    $db->sql_query("INSERT INTO journal (id, date, admin_id, ip, cc, agent, page, action, error) VALUES
        (NULL, now(), '$admin_id', '$ip', '" . $ip_arr['cc'] . "', '$agent', '$page', '$action', '$error')") or
        $stop = mysqli_error();

    return $stop;
}

// function for converting links from unnecessary eyes)
function encodelink($link, $type)
{
    if ($type == 1)
    {
        $link = base64_encode($link);
        $link = str_replace('X', '_', $link);
    } else
    {
        $link = str_replace('_', 'X', $link);
        $link = base64_decode($link);
    }
    return $link;
}

// geobase with russian lang
function geoip($ip, $lang = 'en', $nocache = 0)
{
    global $config, $root;
    require_once ($root . 'include/SxGeo.php');
    require_once ($root . 'include/cngeoip/Geobaza.php');


    if ($config['memcache_ip'])
    {
        $memcached = new Memcache;
        $memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
    }


    $code = md5($ip . $lang);
    if ($memcached)
        $geodata = $memcached->get($code);
    if ($lang == 'ru')
    {
        $name = 'name_ru';
        $name1 = 'RU';
    } else
    {
        $name = 'name_en';
        $name1 = 'EN';
    }
    if (!$geodata || $nocache == 1)
    {
        $SxGeo = new SxGeo($root . 'include/SxGeoMax.dat');
        $geobaza = new Geobaza($root . "include/cngeoip/geobaza.dat");
        $result1 = $geobaza->lookup($ip);

        $types = array();
        if (is_array($result1['items']))
        {
            foreach ($result1['items'] as $key => $value)
            {
                $typen = $value['type'];
                $types[$typen]['name_official'] = $value['name_official'][$name1];
                $types[$typen]['name'] = $value['name'][$name1];
                if ($value['type'] == 'country')
                {
                    $cc = $value['iso_id'];
                }
            }
            $cc1 = $cc;
        }
        $city = $types['locality']['name_official'];
        $country = $types['country']['name'];
        $region = $types['region']['name'];

        $result = $SxGeo->getCityFull($ip);
        $city2 = $result['city'][$name];
        $cc = $result['country']['iso'];
        $timezone = $result['region']['timezone'];

        if (!$cc && $cc1)
            $cc = $cc1;

        if ($cc1 != $cc && !empty($city2))
        {
            $city = $city2;
        } elseif ($cc1 != $cc && empty($city2))
        {
            $city = '';
        } elseif ($cc1 == $cc && !empty($city2))
        {
            $city = $city2;
        }
        $country = $result['country'][$name];


        $geodata['cc'] = $cc;
        $geodata['timezone'] = $timezone;
        $geodata['country'] = $country;
        $geodata['city'] = $city;

        if ($memcached)
            $memcached->set($code, $geodata, MEMCACHE_COMPRESSED, 600);
    }

    return $geodata;
}

// checking for user domain, if it's available
function is_valid_domain_name($domain_name)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name)
        //valid chars check
        && preg_match("/^.{1,253}$/", $domain_name) //overall length check
        && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); //length of each label
}

// domain host detection for joined domains
function get_host()
{
    global $config;
    if ($config['local_proj'] != 1)
    {
        $second_level_domains_regex = '/\.asn\.au$|\.com\.au$|\.net\.au$|\.id\.au$|\.org\.au$|\.edu\.au$|\.gov\.au$|\.csiro\.au$|\.act\.au$|\.nsw\.au$|\.nt\.au$|\.qld\.au$|\.sa\.au$|\.tas\.au$|\.vic\.au$|\.wa\.au$|\.co\.at$|\.or\.at$|\.priv\.at$|\.ac\.at$|\.avocat\.fr$|\.aeroport\.fr$|\.veterinaire\.fr$|\.co\.hu$|\.film\.hu$|\.lakas\.hu$|\.ingatlan\.hu$|\.sport\.hu$|\.hotel\.hu$|\.ac\.nz$|\.co\.nz$|\.geek\.nz$|\.gen\.nz$|\.kiwi\.nz$|\.maori\.nz$|\.net\.nz$|\.org\.nz$|\.school\.nz$|\.cri\.nz$|\.govt\.nz$|\.health\.nz$|\.iwi\.nz$|\.mil\.nz$|\.parliament\.nz$|\.ac\.za$|\.gov\.za$|\.law\.za$|\.mil\.za$|\.nom\.za$|\.school\.za$|\.net\.za$|\.co\.uk$|\.org\.uk$|\.me\.uk$|\.ltd\.uk$|\.plc\.uk$|\.net\.uk$|\.sch\.uk$|\.ac\.uk$|\.gov\.uk$|\.mod\.uk$|\.mil\.uk$|\.nhs\.uk$|\.police\.uk$/';
        $domain = $_SERVER['HTTP_HOST'];
        $domain = explode('.', $domain);
        $domain = array_reverse($domain);
        if (preg_match($second_level_domains_regex, $_SERVER['HTTP_HOST']))
        {
            $domain = "$domain[2].$domain[1].$domain[0]";
        } else
        {
            $domain = "$domain[1].$domain[0]";
        }

        return $domain;
    } else
    {
        return 'localhost';
    }
}

// timezone conversion
function converToTz($time = "", $toTz = '', $fromTz = '')
{
    global $config;
    if (!$toTz) $toTz = $config['proj_timezone'];
    if (!$fromTz) $fromTz = $config['proj_timezone'];
    // timezone by php friendly values
    $date = new DateTime($time, new DateTimeZone($fromTz));
    $date->setTimezone(new DateTimeZone($toTz));
    $time = $date->format('Y-m-d H:i:s');
    return $time;
}


// localization of time by user locale
function timezone_time($format = "r", $timestamp = false, $timezone = false)
{

    $userTimezone = new DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
    $gmtTimezone = new DateTimeZone('GMT');
    $myDateTime = new DateTime(($timestamp != false ? date("r", (int)$timestamp) :
        date("r")), $gmtTimezone);
    $offset = $userTimezone->getOffset($myDateTime);
    return date($format, ($timestamp != false ? (int)$timestamp : $myDateTime->
        format('U')) + $offset);

}

// calculation of the effectiveness of subscribers for traffic exchange
function subs_effect($where, $type=0)
{
$stat=array();  
 if ($type==2) {
  $stat = $where;  
 }   else {
$subscribers = subscribers($where, '', 100000);
if (is_array($subscribers)) {

    foreach ($subscribers as $key => $value) {
      $stat['sended'] += $value['sended'];
      $stat['views'] += $value['views'];
      $stat['clicks'] += $value['clicks'];
      if ($value['del']==1) $stat['delete']++;
      }
   }
   }    
    
    if ($stat['views'] > 0)
    {
        $ctr = round(($stat['views'] / $stat['sended']) * 100, 0);
        if ($stat['clicks'] > 0) $ctr2 = round(($stat['clicks'] / $stat['views']) * 100, 0); else $ctr2 = 0;

        return $effect = $ctr + $ctr2;
    }
   
    return 0;
    
}
// get time
function thetime()
{
    return date("Y-m-d H:i:s");
}
// construct for mysql query
function wherelike($column, $arr)
{
    $all = count($arr);

    if ($all == 1)
    {
        $where = "AND " . $column . " LIKE '%" . text_filter($arr[0]) . "%' ";
    } else
    {
        $where = "AND (";
        $i = 0;
        foreach ($arr as $key => $value)
        {
            if ($i > 0)
                $where .= " OR ";
            $where .= "" . $column . " LIKE '%" . text_filter($value) . "%'";
            $i++;
        }
        $where .= ") ";
    }
    return $where;
}
// for feeds
function get_fcontent($url, $javascript_loop = 0, $timeout = 1, $lang = 'en', $params =
    array())
{
    $url = str_replace("&amp;", "&", urldecode(trim($url)));

    //$cookie = tempnam("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: ' . $lang . '']);
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # required for https urls
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "");
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);
    if (isset($params['proxy']))
    {
        curl_setopt($ch, CURLOPT_PROXY, $params['proxy']);
        if (isset($params['credentials']))
        {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $params['credentials']);
        }
    }
    $content = curl_exec($ch);
    $response = curl_getinfo($ch);
    curl_close($ch);

    if ($response['http_code'] == 301 || $response['http_code'] == 302)
    {
        ini_set("user_agent",
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

        if ($headers = get_headers($response['url']))
        {
            foreach ($headers as $value)
            {
                if (substr(strtolower($value), 0, 9) == "location:")
                    return get_fcontent(trim(substr($value, 9, strlen($value))));
            }
        }
    }

    if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content,
        $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) &&
        $javascript_loop < 5)
    {
        return get_fcontent($value[1], $javascript_loop + 1);
    } else
    {
        return array($content, $response);
    }
}
// create new email
function newmail($admin_id = 0, $email, $title, $content, $lang = '')
{
    global $db;

    $db->sql_query("INSERT INTO mails (id, date, admin_id, email, title, content, create_time, lang) VALUES
        (NULL, now(), '$admin_id', '$email', '$title', '$content', now(), '$lang')") or
        $stop = mysqli_error();
    if (!$stop)
    {
        return 1;
    } else
    {
        return $stop;
    }
}
// get browser language
function get_lang()
{
    preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
        $matches);
    $langs = array_combine($matches[1], $matches[2]);
    foreach ($langs as $n => $v)
        $langs[$n] = $v ? $v : 1;
    arsort($langs);
    $default_lang = key($langs);
    $s = strtok($default_lang, '-');
    return $s;
}
// create alert for user
function alert($text, $admin_id, $type = 'info')
{
    global $db;
    $stop = 1;
    $settings = settings("AND admin_id=".$admin_id."");
    $admin_info = admins("AND id=".$admin_id."");

    $db->sql_query("INSERT INTO alerts (id, admin_id, date, type, text, view) VALUES
        (NULL, '$admin_id', now(), '$type', '$text', '0')") or $stop = mysqli_error();
    
    if ($admin_info[$admin_id]['notif_push']==1) {
        
          $admin_push = subscribers("AND uid=".$admin_id." AND del=0");

$ads['title'] = $settings['siteurl'];
$ads['body'] = text_filter($text);
$ads['icon'] = "https://".$settings['siteurl']."/image/icon.png";
$ads['url'] = "https://".$settings['siteurl']."/admin/index.php";
$site['sid'] = 184;
$site['subid'] = '';
$site['subs_id'] = '';
$site['params'] = 0;

    if (is_array($admin_push)) {
        foreach ($admin_push as $key => $value) { 
          $send = send_push($ads, $site, $value['token'], 1);
          if (preg_match('/NotRegistered/i', $send) == 1) {
           $db->sql_query("UPDATE subscribers SET del=1, last_update=now() WHERE id='".$key."'");       
          } 
        }
    }
      
    }
    return $stop;

}
// get setting list for all users
function settings_all($where = '')
{
    global $db;

    $query = $db->sql_query("SELECT * FROM settings WHERE 1 " . $where . "");
    $query = $db->sql_fetchrowset($query);
    foreach ($query as $key => $value)
    {
        $settings[$value['id']]['admin_id'] = $value['admin_id'];
        $settings[$value['id']]['name'] = $value['name'];
        $settings[$value['id']]['value'] = $value['value'];
        $settings[$value['id']]['created'] = $value['created'];
    }
    return $settings;
}
// get system or user settings
function settings($where = '')
{
    global $db;

    $query = $db->sql_query("SELECT * FROM settings WHERE admin_id=0");
    $query = $db->sql_fetchrowset($query);
    foreach ($query as $key => $value)
    {
        $settings[$value['name']] = $value['value'];
    }
    if ($where)
    {
        $query = $db->sql_query("SELECT * FROM settings WHERE 1 " . $where . "");
        $query = $db->sql_fetchrowset($query);
        if (is_array($query))
        {
            foreach ($query as $key => $value)
            {
                $settings[$value['name']] = $value['value'];
            }
        }
    }
    return $settings;
}
// get interval from date
function DateDiffInterval($sDate1, $sDate2 = '', $sUnit = 'H')
{
    //subtract $sDate2-$sDate1 and return the difference in $sUnit (Days,Hours,Minutes,Seconds)
    if (!$sDate2)
        $sDate2 = date("Y-m-d H:i:s");
    $nInterval = strtotime($sDate2) - strtotime($sDate1);
    if ($sUnit == 'D')
    { // days
        $nInterval = $nInterval / 60 / 60 / 24;
    } else
        if ($sUnit == 'H')
        { // hours
            $nInterval = $nInterval / 60 / 60;
        } else
            if ($sUnit == 'M')
            { // minutes
                $nInterval = $nInterval / 60;
            } else
                if ($sUnit == 'S')
                { // seconds
                }
    return $nInterval;
}

// generate random names for files
function gen_pass($m)
{
    $m = intval($m);
    $pass = "";
    for ($i = 0; $i < $m; $i++)
    {
        $te = mt_rand(48, 122);
        if (($te > 57 && $te < 65) || ($te > 90 && $te < 97))
            $te = $te - 9;
        $pass .= chr($te);
    }
    return $pass;
}
// gen code fom money
function fff($sum)
{
    global $config;
    $code = md5($sum.$config['global_secret']);
    return $code;
}
// resize image
function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 90)
{
    if (!file_exists($src))
        return false;

    $size = getimagesize($src);

    if ($size === false)
        return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc))
        return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height,
        $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;

}
// crop image
function resize($img, $width, $height, $method = 1, $proc = 0)
{
    include_once 'Thumbnail.php';
    include_once 'Thumbnail/Control.php';

    $middleImage = Thumbnail::render($img, array(
        'halign' => 0,
        'valign' => 0,
        'percent' => $proc,
        'method' => $method,
        'width' => $width,
        'height' => $height,
        ));

    Thumbnail::output($middleImage, $img, array(
        'halign' => 0,
        'valign' => 0,
        'percent' => $proc,
        'method' => $method,
        'width' => $width,
        'height' => $height,
        ));

    return 1;
}
// for graphics
function randomColor()
{
    $result = array('rgb', 'hex');
    $rbg = array(
        'r',
        'b',
        'g');
    foreach ($rbg as $col)
    {
        $rand = mt_rand(0, 255);
        $result['rgb'][$col] = $rand;
        $dechex = dechex($rand);
        if (strlen($dechex) < 2)
        {
            $dechex = '0' . $dechex;
        }
        $result['hex'] .= $dechex;
    }
    return $result;
}
// check url from feed for the right work
function check_http_status($url, $ssl = 0)
{
    $user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($ssl == 1)
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $page = curl_exec($ch);

    $err = curl_error($ch);
    if (!empty($err))
        return $err;

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpcode;
}

// for sorting ads in news.php
function multiSort($dataArray, $sortColumn, $sortOrder = SORT_DESC, $sortComparsion =
    SORT_REGULAR)
{
    $sortArray = array();
    foreach ($dataArray as $subArray)
    {
        $sortArray[] = $subArray[$sortColumn];
    }
    array_multisort($sortArray, $sortOrder, $sortComparsion, $dataArray, $sortOrder,
        $sortComparsion);
    return $dataArray;
}


function variables()
{ //Analyzer of variables
    if ($_GET)
    {
        echo "<b>GET</b> - ";
        $a = 0;
        foreach ($_GET as $var_name => $var_value)
        {
            if ($a == 0)
            {
                echo "$var_name=$var_value";
            } else
            {
                echo ", $var_name=$var_value";
            }
            $a++;
        }
    }
    if ($_POST)
    {
        echo "<br /><br /><b>POST</b> - ";
        $a = 0;
        foreach ($_POST as $var_name => $var_value)
        {
            if ($a == 0)
            {
                echo "$var_name=$var_value";
            } else
            {
                echo ", $var_name=$var_value";
            }
            $a++;
        }
    }
    if ($_COOKIE)
    {
        echo "<br /><br /><b>COOKIE</b> - ";
        $a = 0;
        foreach ($_COOKIE as $var_name => $var_value)
        {
            if ($a == 0)
            {
                echo "$var_name=$var_value";
            } else
            {
                echo ", $var_name=$var_value";
            }
            $a++;
        }
    }
    if ($_FILES)
    {
        echo "<br /><br /><b>FILES</b> - ";
        $a = 0;
        foreach ($_FILES as $var_name => $var_value)
        {
            if ($a == 0)
            {
                echo "$var_name=$var_value";
            } else
            {
                echo ", $var_name=$var_value";
            }
            $a++;
        }
    }
}
// get mail template and replace macroses
function mail_templ($file, $macros)
{
    global $root, $db;
    if ($file && $macros)
    {
        $dir = $root . "admin/assets/email/";
        $get_file = file_get_contents($dir . $file);
        if ($get_file != false)
        {
            foreach ($macros as $key => $value)
            {

                $get_file = str_replace($key, $value, $get_file);

            }

            return $get_file;

        }
        return false;
    }
    return false;
}
// send email
function mail_send($email, $smail, $subject, $message, $admin_id = "", $lang = "en", $pr = "", $dev = 0, $id='')
{
    global $config;
    $settings = settings();
    $email = text_filter($email);
    $smail = text_filter($smail);
    $title = $subject;
    //$subject = text_filter($subject);
    $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
    $pr = (!$pr) ? "3" : "" . intval($pr) . "";
    $mheader = "MIME-Version: 1.0\n" . "Content-Type: text/html; charset=utf-8\n" .
        "Reply-To: \"$smail\" <$smail>\n" . "From: \"$smail\" <$smail>\n" .
        "Return-Path: <$smail>\n" . "X-Priority: $pr\n";

    $key = md5($config['global_secret'] . $admin_id);
    $key = $key . "&id=" . $admin_id;
    $macros['[SITE_URL]'] = $settings['siteurl'];
    $macros['[SITE_NAME]'] = $settings['sitename'];
    $macros['[TITLE]'] = $title;
    $macros['[CONTENT]'] = $message;
    $macros['[TELEGRAM]'] = $settings['telegram'];
    $macros['[EMAIL]'] = $settings['support_mail'];
    $macros['[EMAIL_KEY]'] = $key;
    $macros['[ID]'] = $id;

    $content = mail_templ("basic_" . $lang . ".html", $macros);

    if ($content)
    {
        mail($email, $subject, $content, $mheader);
        if ($dev == 1)
        {
            return $content;
        } else
            return true;
    } else
    {
        return false;
    }
}
// filter for all text inputs
function text_filter($message, $type = "")
{
    if (intval($type) == 2)
    {   
        $message = strip_tags(urldecode($message));
        $message = htmlspecialchars(trim($message), ENT_QUOTES);
    } elseif (intval($type) == 3)
    {   
        $message = htmlspecialchars(trim($message), ENT_QUOTES);
    } else
    {   
        $message = sql_filter($message);
        $message = strip_tags(urldecode($message));
        $message = htmlspecialchars(trim($message), ENT_QUOTES);
    }
    return $message;
}
// check file size
function files_size($size)
{ //Size filter
    if ($size >= 1073741824)
    {
        $mysize = "" . round(($size / 1073741824), 2) . " GB";
    } elseif ($size >= 1048576)
    {
        $mysize = "" . round(($size / 1048576), 2) . " MB";
    } elseif ($size >= 1024)
    {
        $mysize = "" . round(($size / 1024), 2) . " KB";
    } else
    {
        $mysize = "" . $size . " Bytes";
    }
    return $mysize;
}
// go back in time <===
function gettime($back = 0)
{
    $date = new DateTime();
    $date->modify('-' . $back . ' day');
    return $date->format('Y-m-d');
}

// pagination
function num_page($numstories, $numpages, $storynum, $module_link = "")
{
    global $pagenum;
    $pagenum = (intval($pagenum)) ? $pagenum : 1;
    if ($numpages > 1)
    {
        echo "<center><div class=numpage>" . _PAGE . ": ";
        if ($pagenum > 1)
        {
            $prevpage = $pagenum - 1;
            echo "<a href=\"" . $module_link . "page=$prevpage\">&lt;&lt;&nbsp;</a>";
        }
        echo " ";
        for ($i = 1; $i < $numpages + 1; $i++)
        {
            if ($i == $pagenum)
            {
                echo " $i ";
            } else
            {
                if ((($i > ($pagenum - 8)) && ($i < ($pagenum + 8))) or ($i == $numpages) || ($i ==
                    1))
                {
                    echo "<a href=\"" . $module_link . "page=$i\"><b>$i</b></a>";
                }
            }
            if ($i < $numpages)
            {
                if (($i > ($pagenum - 9)) && ($i < ($pagenum + 8)))
                    echo " | ";
                if (($pagenum > 9) && ($i == 1))
                    echo " | ...";
                if (($pagenum < ($numpages - 8)) && ($i == ($numpages - 1)))
                    echo "... | ";
            } else
            {
                echo "";
            }
        }
        if ($pagenum < $numpages)
        {
            $nextpage = $pagenum + 1;
            echo "<a href=\"" . $module_link . "page=$nextpage\">&nbsp;&gt;&gt;</a>";
        }
        echo "<br /><font class=small>($storynum " . _ONPAGE . ")</font></div></center>";
    }
}
// check file type
function check_file($type, $atypefile)
{
    $strtypefile = str_replace(",", "|", $atypefile);
    if (!preg_match("/" . $strtypefile . "/i", $type) || preg_match("/php|php3|php4|php5|php6|js|htm|html|phtml|cgi|pl|perl|asp/i",
        $type))
    {
        return "Error extension: $type";
    }
}
// check image size
function check_size($file, $width, $height)
{ //Check size upload file
    list($imgwidth, $imgheight) = getimagesize($file);
    if ($imgwidth > $width || $imgheight > $height)
    {
        return "File site too big";
    }
}
// file upload
function upload($directory, $atypefile, $maxsize, $namefile, $width, $height, $key)
{
    global $stop;
    if (is_uploaded_file($_FILES[$key]['tmp_name']))
    {
        if ($_FILES['img']['size'] > $maxsize)
        {
            $stop = "the weight more than $maxsize b!";
            return $stop;
        } else
        {
            $type = explode(".", $_FILES[$key]['name']);
            $type = strtolower(end($type));
            if (!check_file($type, $atypefile) && !check_size($_FILES[$key]['tmp_name'], $width,
                $height))
            {
                if (file_exists("" . $directory . "/" . $type . ""))
                {
                    $stop = "A file with this name already exists on the server!";
                    return $stop;
                } else
                {
                    $newname = ($namefile) ? "" . $namefile . "-" . gen_pass(10) . "." . $type . "" :
                        "" . gen_pass(15) . "." . $type . "";
                    $res = copy($_FILES[$key]['tmp_name'], "" . $directory . "/" . $newname . "");
                    if (!$res)
                    {
                        $stop = "An error occurred while writing to the file!";
                        return $stop;
                    } else
                    {
                        return $newname;
                    }
                }
            } else
            {
                $stop = (!check_file($type, $atypefile)) ? "" . check_size($_FILES[$key]['tmp_name'],
                    $width, $height) . "" : "" . check_file($type, $atypefile) . "";
                return $stop;
            }
        }
    } else
    {
        $stop = "An error occurred while loading the source file!";
        return $stop;
    }
}
// browser info, only for users
function getBrowser($u_agent)
{
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";
    if (preg_match('/linux/i', $u_agent))
    {
        $platform = 'Linux';
    } elseif (preg_match('/J2ME/i', $u_agent))
    {
        $platform = 'JAVA';
    } elseif (preg_match('/iPhone/i', $u_agent))
    {
        $platform = 'iPhone';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent))
    {
        $platform = 'Mac';
    } elseif (preg_match('/windows|win32/i', $u_agent))
    {
        $platform = 'Windows';
    } elseif (preg_match('/Android/i', $u_agent))
    {
        $platform = 'Android';
    } elseif (preg_match('/SymbianOS/i', $u_agent))
    {
        $platform = 'SymbianOS';
    }
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Opera Mini/i', $u_agent))
    {
        $bname = 'Opera Mini';
        $ub = "OperaMini";
    } elseif (preg_match('/iPad/i', $u_agent))
    {
        $bname = 'iPad';
        $ub = "iPad";
    } elseif (preg_match('/iPhone/i', $u_agent))
    {
        $bname = 'iPhone';
        $ub = "iPhone";
    } elseif (preg_match('/Firefox/i', $u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Opera/i', $u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/YaBrowser/i', $u_agent))
    {
        $bname = 'Yandex Browser';
        $ub = "YaBrowser";
    } elseif (preg_match('/OPR/i', $u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Chrome/i', $u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Netscape/i', $u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Android/i', $u_agent))
    {
        $bname = 'Android';
        $ub = "AndroidBrowser";
    }
    $known = array(
        'Version',
        $ub,
        'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches))
    {
        //we have no matching number just continue
    }
    $i = count($matches['browser']);
    if ($i != 1)
    {
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub))
        {
            $version = $matches['version'][0];
        } else
        {
            @$version = $matches['version'][1];
        }
    } else
    {
        $version = $matches['version'][0];
    }
    if ($version == null || $version == "")
    {
        $version = "?";
    }
    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern,
        'short' => $ub);
}