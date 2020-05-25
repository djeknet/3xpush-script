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
if ($check_login==false) {
    exit;
}

$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];


$path =$root . '/views/emoji_'.$lang.'.txt';

if(file_exists($path)) {
    $content = file_get_contents($path);
}
else {
    $content = '';
}
echo $content;