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

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 1);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");

require_once("../include/mysql.php");
require_once("../include/func.php");
require_once("../include/info.php");
require_once("../include/stat.php");
require_once("../include/SxGeo.php");
include("forms.php");

if ($config['memcache_ip']) {
@$memcached = new Memcache;
@$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}
$ip = getenv("REMOTE_ADDR");
$checkip = check_ip($ip);
// if ip is blocked, then show a blank page
if ($checkip==1) {
    exit;
}
@$dev=intval($_GET['dev']);
$SxGeo = new SxGeo('../include/SxGeoMax.dat');

if ($config['local_proj']==1) {
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 1);
 } else {
error_reporting(0);
ini_set('display_errors', 0);
 }

$virtual_exit = intval($_GET['virtual_exit']);
if ($virtual_exit) {
unset($_COOKIE["vid"]);
setcookie("vid", null, -1);
}

header('Content-Type: text/html; charset=utf-8');


$check_login = check_login();
if ($check_login['id']) $admid = $check_login['id']; else  $admid=0;
if ($check_login['root']!=1) $dev=0; // only super admin can be dev

// checking of incoming data
if ($settings['check_inputs']==1) {
$check_request = check_input($_REQUEST, 1);
if ($check_request==1) {
$num_request=0;   
if ($memcached) { 
if ($admid) $code1 = $admid; else  $code1 = $ip;  
$code = md5('badrequest'.$code1);
$num_request = $memcached->get($code);
$num_request= $num_request + 1;
$memcached->set($code, $num_request, false, time() + 86000);
}

    // if the user is not marked as verified, then block it
    if ($check_login['good_user']!=1) {
    // if user lock is enabled
    if ($settings['check_inputs_blockuser']==1 && $num_request >= $settings['check_inputs_count'] && $admid) {
        $reason = "Suspicious site requests";
        $info1 = "User blocked! ";
        $db->sql_query("UPDATE admins SET status='2', wayban='".$reason."' WHERE id='".$admid."'");
        $blockuser=1;
    }
    // if ip block is enabled
    if ($settings['check_inputs_blockip']==1 && $num_request >= $settings['check_inputs_count']) {
        $blackips = $ip.",".$settings['black_ip'];
        $blackipsarr = array_unique(explode(',', $blackips));
        $blackips = implode(',', $blackipsarr);
        $info1 .= "IP blocked!";
        $db->sql_query("UPDATE settings SET value='".$blackips."' WHERE name='black_ip' and admin_id=0");
        $blockuser=1;
    }
    }
    // sending notification to admin 
    if ($settings['check_inputs_alert']==1) {
    $page = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
    $request = json_encode($_REQUEST);
    $request = addslashes($request);
    $text = "<b>Suspicious site requests!</b>\n
    Login: ".$check_login['login']."\n
    IP: ".$ip."\n
    Browser: ".$agent."\n
    Page: ".$page."\n
    Request: ".$request."\n".$info1."";
    
   alert($text, 1, 'warning'); 
   }
}
}

if ($blockuser==1) {
echo "user blocked";
logout();
$check_login=false;
exit;
}

if ($check_login['id']) {
$balance = balance($check_login['getid']);
list($lang) = $db->sql_fetchrow($db->sql_query("SELECT value FROM settings WHERE name='lang' AND admin_id=".$check_login['id'].""));
}

$settings = settings("AND admin_id=".$admid."");
date_default_timezone_set($settings['timezone']);


if (!$lang) {$lang = get_lang();}
if ($lang != 'ru') $lang='en';

include("langs/".$lang.".php");

if ($_GET['logout']==1) {
jset($admid, "Logout"); 
logout();
redirect("?m=login");
$check_login=false;
}

$module = text_filter($_GET['m']);

if ($module=='register' && $check_login==false) {
  include("register.php");
  exit;
} elseif ($module=='forget' && $check_login==false) {
  include("forget.php");
  exit;
} elseif ($check_login==false) {
  include("login.php");
  exit;
}

$modules = array('traf_exchange_stat','traf_exchange','my_send', 'forget', 'faq', 'postback_log', 'refs','a_analitika','landstat','a_journal', 'a_clickstat','a_nopaystat', 'a_crons','a_content','a_reports','a_landings', 'a_news', 'a_faq', 'rules', 'a_mails', 'a_payment', 'a_users', 'balance', 'account', 'alerts', 'browser', 'landinghtml', 'options', 'sites', 'daystat', 'subid', 'sitestat', 'advstat', 'feedstat', 'regionstat', 'groupstat', 'subscribers', 'sended', 'clicks', 'feeds', 'admins');
if ($module && !in_array($module, $modules)) $module = 'home';
if (!$module) $module = 'home';

$bodyOpenClass = isset($_COOKIE['body_open_class']) ? 1 : 0;

$virtual_id = intval($_GET['virtual_id']);

// logging in as a user, write cookie to receive data under the user id
if ($virtual_id && $check_login['root']==1 && $check_login['role']==1) {
setcookie("vid", $virtual_id, time() + 86000);
}

include("header.php");
include("$module.php");
include("footer.php");
?>
