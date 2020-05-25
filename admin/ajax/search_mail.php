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
include("../langs/".$lang.".php");

$param = text_filter($_GET['param']);
$params = str_replace('|', "&", $param);
parse_str($params);

if ($search_lang) {
    if ($search_lang==1) {
     $settings_all = settings_all("AND name='lang' AND value IN ('ru', 'en')");  
    } else {
      $settings_all = settings_all("AND name='lang' AND value='".$search_lang."'");   
    }

   $admin_ids=array();
   if(is_array($settings_all)) {
     foreach ($settings_all as $key => $value) {
       if ($value['admin_id']!=0) $admin_ids[1][] = $value['admin_id'];
       $filters[1]=1;
     }
   }   
   // фильтр по балансу
   if ($search_balance) {
    if ($search_balance==-1) {
       $sql = "SELECT admin_id FROM balance WHERE summa = 0";   
    } else {
       $sql = "SELECT admin_id FROM balance WHERE summa >= ".$search_balance;  
    }
   
  
    $info = $db->sql_query($sql);
    $info = $db->sql_fetchrowset($info);
     if(is_array($info)) {
        foreach ($info as $key => $value) {
            $admin_ids[2][] = $value['admin_id'];
             $filters[2]=1;
            }
        } 
   }
   // фильтр по сайтам
   if ($search_sites) {
    if ($search_sites==-1) {
    $sql = "SELECT admin_id FROM `sites` WHERE 1 GROUP by admin_id HAVING COUNT(id) = 0";
    } else {
    $sql = "SELECT admin_id FROM `sites` WHERE 1 GROUP by admin_id HAVING COUNT(id) >= ".$search_sites;    
    }
    $info = $db->sql_query($sql);
    $info = $db->sql_fetchrowset($info);
     if(is_array($info)) {
        foreach ($info as $key => $value) {
            $admin_ids[3][] = $value['admin_id'];
            $filters[3]=1;
            }
        } 
   }

 if (count($filters)>1) {
    $result = call_user_func_array('array_intersect', $admin_ids);
    foreach ($result as $key => $value) {
    $admins[] = $value;
   }  
 } else {
   foreach ($admin_ids as $key => $value) {
     foreach ($value as $num => $id) {
    $admins[] = $id;
    }
   }
 }
   
   $admins = implode(',', $admins) ;                      
echo "<input type=\"text\" name=\"admin_ids\" value=\"$admins\" size=\"50\" />";
} else {
    status(_NOSELECTED, 'warning');
}