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

@$dev= intval($_GET['dev']);

require_once("include/mysql.php");
require_once("include/func.php");
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

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

$SxGeo = new SxGeo('include/SxGeoMax.dat');
$sid = intval($_GET['sid']);
$land_id = intval($_GET['lid']); // landing id
$advid = intval($_GET['advid']);
$advsid = intval($_GET['advsid']); // id in advs table, webmaster statistics for ads
$subs_id = intval($_GET['subs_id']); // subscriber id for viewing update
$type = intval($_GET['type']);
$sub = text_filter($_GET['sub']);
$ip = getenv("REMOTE_ADDR");

$geoip = geoip_new($ip);

$agent = getenv("HTTP_USER_AGENT");

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

list($admin_id) = $db->sql_fetchrow($db->sql_query("SELECT admin_id FROM  sites WHERE id = '".$sid."'"));
if (!$admin_id) $admin_id=0;

if ($subs_id) {
 list($old_cc) = $db->sql_fetchrow($db->sql_query("SELECT cc FROM  subscribers WHERE id = '".$subs_id."'"));   
 if ($geoip['cc']!=$old_cc) {
$db->sql_query("UPDATE subscribers SET ip='".$ip."', cc='".$geoip['cc']."', region='".$geoip['region']."', city='".$geoip['city']."', ip_range='".$ip_range."' WHERE id='".$subs_id."'");    
 } 
 
if ($advid) $db->sql_query("UPDATE send_loop SET is_view=1 WHERE subs_id='".$subs_id."' AND myads_id='".$advid."'");

} else {
  $old_cc = $geoip['cc']; 
}
$settings = settings("AND admin_id=".$admin_id."");

$deviceDetector = new DeviceDetector($agent);
$deviceDetector->parse();

$br = $deviceDetector->getClient();

list($brid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  browsers WHERE `key` = '".$br['name']."'"));

// push new subscriber
if ($sid && $type==1) {
$db->sql_query('INSERT INTO daystat (date, admin_id, sid, subid, requests)
        VALUES (CURRENT_DATE(), "' . $admin_id . '", ' . $sid . ', "'.$sub.'", 1)
         ON DUPLICATE KEY UPDATE requests=requests+1');

$db->sql_query("INSERT INTO region_stat (date,  admin_id, sid, cc, requests)
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $sid . ", '".$old_cc."', 1)
                  ON DUPLICATE KEY UPDATE
                  requests = requests + 1");

$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'requests', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");

$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, requests)
                  VALUES (CURRENT_DATE(), '" . $brid . "',  '" . $admin_id . "', '".$sid."', 1)
                  ON DUPLICATE KEY UPDATE
                  requests = requests + 1");

if ($land_id) {
 $db->sql_query('INSERT INTO landing_stat (id, date, admin_id, sid, subid, land_id, requests)
        VALUES (NULL, CURRENT_DATE(), "'.$admin_id.'", ' . $sid . ', "'.$sub.'", '.$land_id.', 1)
         ON DUPLICATE KEY UPDATE requests=requests+1');     
}

// push view
} elseif ($sid && $type==2) {
$db->sql_query('INSERT INTO daystat (date,  admin_id, sid, subid, img_views) 
        VALUES (CURRENT_DATE(),  "' . $admin_id . '", ' . $sid . ', "'.$sub.'", 1) 
         ON DUPLICATE KEY UPDATE img_views=img_views+1') or $errors[] = "daystat: ".mysqli_error();

$db->sql_query("INSERT INTO region_stat (date, admin_id, sid, cc, img_views) 
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $sid . ", '".$old_cc."', 1) 
                  ON DUPLICATE KEY UPDATE 
                  img_views = img_views + 1") or $errors[] = "region_stat: ".mysqli_error();

$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, img_views) 
                  VALUES (CURRENT_DATE(), '" . $brid . "', '" . $admin_id . "', '".$sid."', 1) 
                  ON DUPLICATE KEY UPDATE 
                  img_views = img_views + 1") or $errors[] = "browser_stat: ".mysqli_error();

$db->sql_query("INSERT INTO total_stat (admin_id, name, value) 
                  VALUES ('" . $admin_id . "', 'push_views', 1) 
                  ON DUPLICATE KEY UPDATE 
                  value = value + 1") or $errors[] = "total_stat: ".mysqli_error();

$admin_id2 = $admin_id;

if ($advid) {
    
if ($memcached) {
$code = md5($advid.$sid.$agent.$ip);
$view_status = $memcached->get($code); // get cache of recorded view from rewrite
$cache_time = $settings['send_every'] * 60 * 60;
}
    
if (!$view_status) { 
$db->sql_query("UPDATE myads SET views=views+1 WHERE id='$advid'"); 
}
}

if ($advsid) {
$db->sql_query("UPDATE advs SET views=views+1 WHERE id='$advsid'");
}

if ($subs_id) {
    if (!$settings['send_afterview']) $settings['send_afterview'] = 4;
$db->sql_query("UPDATE subscribers SET views=views+1, next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_afterview']." HOUR) WHERE id='$subs_id'");   
}

// closed push
} elseif ($sid && $type==6) {
$db->sql_query('INSERT INTO daystat (date,  admin_id, sid, subid, closed)
        VALUES (CURRENT_DATE(), "' . $admin_id . '", ' . $sid . ', "'.$sub.'", 1)
         ON DUPLICATE KEY UPDATE closed=closed+1');

$db->sql_query("INSERT INTO region_stat (date, admin_id, sid, subid, cc, closed)
                  VALUES (CURRENT_DATE(), '" . $admin_id . "', " . $sid . ", '".$sub."', '".$old_cc."', 1)
                  ON DUPLICATE KEY UPDATE closed = closed + 1");

$db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, closed)
                  VALUES (CURRENT_DATE(), '" . $brid . "', '".$admin_id."', '".$sid."', 1)
                  ON DUPLICATE KEY UPDATE
                  closed = closed + 1");

$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'push_closed', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");

} elseif ($type==3) {
$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'worker_install', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");

} elseif ($type==4) {
$db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES ('" . $admin_id . "', 'worker_activate', 1)
                  ON DUPLICATE KEY UPDATE
                  value = value + 1");
                  
 // block requets                 
} elseif ($type==7) {
$db->sql_query('INSERT INTO daystat (date, admin_id, sid, subid, blocked_requests)
        VALUES (CURRENT_DATE(), "' . $admin_id . '", ' . $sid . ', "'.$sub.'", 1)
         ON DUPLICATE KEY UPDATE blocked_requests=blocked_requests+1');

         $db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, blocked_requests)
                  VALUES (CURRENT_DATE(), '" . $brid . "', '".$admin_id."', '".$sid."', 1)
                  ON DUPLICATE KEY UPDATE
                  blocked_requests = blocked_requests + 1");
     if ($land_id) {
     $db->sql_query('INSERT INTO landing_stat (id, date, admin_id, sid, subid, land_id, blocked_requests)
        VALUES (NULL, CURRENT_DATE(), "'.$admin_id.'", ' . $sid . ', "'.$sub.'", '.$land_id.', 1)
         ON DUPLICATE KEY UPDATE blocked_requests=blocked_requests+1');   
}             
}

if ($dev==1) {
    echo "errors:";
    print_r($errors);

}