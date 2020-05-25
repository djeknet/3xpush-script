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

// Tracking openings and clicks from email
error_reporting(0);
ini_set('display_errors', 0);
 

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/info.php");
require_once('include/SxGeo.php');

$SxGeo = new SxGeo('include/SxGeoMax.dat');

if ($memcache_local_ip) {
$memcached_local = new Memcache;
$memcached_local->pconnect($memcache_local_ip, $memcache_local_port);
}

$ip = getenv("REMOTE_ADDR");
$id = intval($_GET['id']);
$type = intval($_GET['type']);
$url = text_filter($_GET['url']);
$code = md5("mail_view".$id.$ip);
if ($memcache_local_ip)  $is_tracked = $memcached_local->get($code);


$hour = date("G");

 // view        
if ($id && !$is_tracked && !$type) {

$db->sql_query("UPDATE mails SET views=views+1 WHERE id='$id'");   
$db->sql_query('INSERT INTO mails_stat (date, views) VALUES (CURRENT_DATE(), 1) ON DUPLICATE KEY UPDATE  views = views + 1');
$db->sql_query('INSERT INTO mails_stat_hour (date, hour, views) VALUES (CURRENT_DATE(), '.$hour.', 1) ON DUPLICATE KEY UPDATE  views = views + 1');

if ($memcache_local_ip)  $memcached_local->set($code, 1, false, time() + 86000);  
}
// click
if ($type==1 && $id && $url) {
$db->sql_query("UPDATE mails SET clicks=clicks+1 WHERE id='$id'");   
$db->sql_query('INSERT INTO mails_stat (date, clicks) VALUES (CURRENT_DATE(), 1) ON DUPLICATE KEY UPDATE  clicks = clicks + 1');
$db->sql_query('INSERT INTO mails_stat_hour (date, hour, clicks) VALUES (CURRENT_DATE(), '.$hour.', 1) ON DUPLICATE KEY UPDATE  clicks = clicks + 1');

header("Location: ".$url."");   
}

if (!$type) {
$image = 'image/favicon.png';
$get_image = file_get_contents($image);

header('Content-type: image/png');

echo $get_image;
}

?>
