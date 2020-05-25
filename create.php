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

error_reporting(0);
ini_set('display_errors', 0);

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/info.php");
require_once('include/SxGeo.php');

include_once 'include/devicedetector/spyc/Spyc.php';
include_once 'include/devicedetector/autoload.php';
$SxGeo = new SxGeo('include/SxGeoMax.dat');

use DeviceDetector\DeviceDetector;

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

$site_id = intval($_GET['sid']);
$landing_id = intval($_GET['lid']);
$user_id = intval($_GET['uid']); // id в admins
$token = text_filter($_GET['t']);
$sub = text_filter($_GET['sub']);
$tag = text_filter($_GET['tag']);
$ptitle = text_filter($_GET['ptitle']); // заголовок страницы
$user_from = text_filter($_GET['uf']); // от куда юзер пришел
$type = intval($_GET['type']);
$ref = htmlspecialchars(stripslashes(getenv("HTTP_REFERER")));
$locale = get_lang();
$ip = getenv("REMOTE_ADDR");
$agent = getenv("HTTP_USER_AGENT");

$check_login = check_login();
if ($check_login['id']) $user_id = $check_login['id']; // если это авторизованный юзер, то присваиваем его id
if ($ptitle) {
    $ptitle = str_replace("'", "", $ptitle);
}
$result = $SxGeo->getCityFull($ip);

$geoip = geoip_new($ip);
$city = $geoip['city'];
$cc = $geoip['cc'];
$region = $geoip['region'];
$country = $geoip['country'];
if ($result['country']['iso']==$cc) {
$timezone = $result['region']['timezone'];    
}

if ($token && $site_id) {
list($isid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  subscribers WHERE token = '$token'"));
if ($isid) {
echo 'no';
}

$deviceDetector = new DeviceDetector($agent);
$deviceDetector->parse();

$device = $deviceDetector->getDeviceName();
$brand = $deviceDetector->getBrandName();
$model = $deviceDetector->getModel();
$os = $deviceDetector->getOs();
$br = $deviceDetector->getClient();

list($brid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  browsers WHERE `key` = '".$br['name']."'"));
list($admin_id, $postback) = $db->sql_fetchrow($db->sql_query("SELECT admin_id, postback FROM  sites WHERE id = '".$site_id."'"));
if (!$admin_id) $admin_id=0;

$settings = settings("AND admin_id='$admin_id'");

$next_send = $settings['send_fornew'];
 if (!$next_send) $next_send = 4;
          
$db->sql_query("INSERT INTO subscribers (id, admin_id, uid, sid, token, ip, browser, os, browser_short, lang, device, brand, model, createtime, subid, tag, referer, subs_type, cc, country, region, city, timezone, sender_id, browser_id, next_send, page_title, user_from) VALUES
(NULL, '".$admin_id."', '".$user_id."', '".$site_id."', '".$token."', '".$ip."', '".$agent."', '".$os['name']."', '".$br['name']."',  '".$locale."',  '".$device."', '".$brand."', '".$model."', now(), '".$sub."', '".$tag."', '".$ref."', '".$type."', '".$cc."', '".$country."', \"".$region."\", \"".$city."\", '".$timezone."', '".$settings['server_key']."', '".$brid."', DATE_ADD(NOW(), INTERVAL ".$next_send." HOUR), '".$ptitle."', '".$user_from."')")  or die ("<center><br>".mysql_error()."</center>");

$subs_id = $db->sql_nextid();

$db->sql_query("INSERT INTO daystat (date, admin_id, sid, subid, subscribers)
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $site_id . ", '".$sub."', 1)
                  ON DUPLICATE KEY UPDATE
                  subscribers = subscribers + 1");

$db->sql_query("INSERT INTO region_stat (date, admin_id, sid, cc, subscribers)
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $site_id . ", '".$cc."', 1)
                  ON DUPLICATE KEY UPDATE
                  subscribers = subscribers + 1");

$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, subscribers)
                  VALUES (CURRENT_DATE(), '" . $brid . "', '" . $admin_id . "', '".$site_id."', 1)
                  ON DUPLICATE KEY UPDATE
                  subscribers = subscribers + 1");

$db->sql_query("UPDATE sites SET last_subscribe=now(), subscribers=subscribers+1 WHERE id='".$site_id."'");

$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'subscribers', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");
if ($landing_id) {
    $db->sql_query("UPDATE landings SET subs=subs+1 WHERE id=" . $landing_id . "");
    
    $db->sql_query('INSERT INTO landing_stat (id, date, admin_id, sid, subid, land_id, subs)
        VALUES (NULL, CURRENT_DATE(), "'.$admin_id.'", ' . $site_id . ', "'.$sub.'", '.$landing_id.', 1)
         ON DUPLICATE KEY UPDATE subs=subs+1');  
}

setcookie("subs_id", $subs_id, time() + 86000 * 360, "/");

echo 'ok';

}
