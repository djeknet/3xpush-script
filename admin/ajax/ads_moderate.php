<?php

ini_set('display_errors', 0);
error_reporting(0);
require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/stat.php");
require_once("../../include/info.php");
require_once("../forms.php");
header('Content-Type: text/html; charset=utf-8');

$check_login = check_login();
if ($check_login['root']!=1) {
  exit;
}
$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];
include("../langs/".$lang.".php");

$param = text_filter($_GET['param']);
$params = explode('|', $param);
$id = intval($params[0]);
$status = intval($params[1]);
$time = text_filter($params[2]);

if ($id) {
    if ($status==1) {
   $db->sql_query("UPDATE myads SET  moderate=1, last_edit=now(), way_block='' WHERE id='$id' AND last_edit < '$time'");
       $admins_alerts = myads("AND id IN (".$id.")");
    foreach ($admins_alerts as $key => $value) {
     $admins[$value['admin_id']] = $value['admin_id'];
    }
    foreach ($admins as $key => $value) {
    $lang = admin_lang($key);    
    $text_alert = text_alert('MODERATION', $lang);
       alert($text_alert, $key, 'info');
    }
  echo "<b class=green>Approved</b>";
  }
 
}