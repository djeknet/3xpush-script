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
$params = explode('|', $param);
$user_id = intval($params[0]);
$check_code = text_filter($params[1]);

if (!$user_id || !$check_code) {echo 'params error';exit;}

$code = md5($config['global_secret'].$user_id);
if ($check_code!=$code) {echo 'check code error';exit;}

$admin_info = admins_balance("AND a.id=$user_id");
$admins = admins_balance();

foreach ($admins as $key => $value) {
  $all_summa += $value['summa'];
}
if ($admin_info[$user_id]['email_check']==1) $check = 'checked';
if ($admin_info[$user_id]['ref_active']==1) $ref_active = 'checked';
if ($admin_info[$user_id]['role'] == 1) $info['role'] = "admin";  else $info['role'] = "guest";    
if ($admin_info[$user_id]['root'] == 1) $info['root'] = "<strong>SUPERADMIN</strong>"; else $info['root'] = "NO";
if ($admin_info[$user_id]['owner_id']) $info['owner'] = $admins[$admin_info[$user_id]['owner_id']]['login']; else $info['owner'] = '-';
if ($admin_info[$user_id]['get_mail'] == 1) $info['get_mail'] = "<span class=green>"._YES."</span>"; else $info['get_mail'] = "<span class=red>"._NO."</span>"; 
if ($admin_info[$user_id]['promo_mail'] == 1) $info['promo_mail'] = "<span class=green>"._YES."</span>"; else $info['promo_mail'] = "<span class=red>"._NO."</span>"; 
if ($admin_info[$user_id]['auto_money'] == 1) $info['auto_money'] = "<span class=green>"._YES."</span>"; else $info['auto_money'] = "<span class=red>"._NO."</span>"; 
if ($admin_info[$user_id]['ref_active'] == 1) $info['ref_active'] = "<span class=green>"._YES."</span>"; else $info['ref_active'] = "<span class=red>"._NO."</span>"; 
if ($admin_info[$user_id]['allmoney'] > 0 && $admin_info[$user_id]['spend_money'] > 0) {
   $spend_money = round(($admin_info[$user_id]['spend_money']/$admin_info[$user_id]['allmoney'])*100, 0); 
} else $spend_money = 0; 
if ($admin_info[$user_id]['allmoney'] > 0 && $admin_info[$user_id]['got_money'] > 0) {
   $got_money = round(($admin_info[$user_id]['got_money']/$admin_info[$user_id]['allmoney'])*100, 0); 
} else $got_money = 0;  
  
if ($admin_info[$user_id]['summa']) {
     $summa_proc = round(($admin_info[$user_id]['summa']/$all_summa)*100, 1); 
} else $summa_proc=0; 


if ($admin_info[$user_id]['got_money'] > 0) {
list($got_today) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE admin_id='".$user_id."' AND date=CURRENT_DATE()"));
list($got_yesterday) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE admin_id='".$user_id."' AND date=DATE_ADD( NOW( ) , INTERVAL -1 DAY )"));
list($got_week) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE admin_id='".$user_id."' AND date >= DATE_ADD( NOW( ) , INTERVAL -7 DAY )"));
list($got_last_week) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE admin_id='".$user_id."' AND date >= DATE_ADD( NOW( ) , INTERVAL -14 DAY ) AND date <= DATE_ADD( NOW( ) , INTERVAL -7 DAY )"));
}

if ($got_today > 0 && $got_yesterday >0) {
   $got_diff = round($got_today - $got_yesterday, 2); 
   if ($got_diff > 0 ) {
    $got_diff = "<span class=green>+".$got_diff."</span>";
   } else {
    $got_diff = "<span class=red>".$got_diff."</span>";
   }
}  else $diff = '-'; 

if ($got_last_week && $got_week) {
    $got_diff_week = round($got_week - $got_last_week, 2); 
   if ($got_diff_week > 0 ) {
    $got_diff_week = "<span class=green>+".$got_diff_week."</span>";
   } else {
    $got_diff_week = "<span class=red>".$got_diff_week."</span>";
   }
} else $got_diff_week = '-';


   
                 
echo "<table width=100%><tbody>";
echo "<tr><td width=40%>"._LOGIN."</th><td>".$admin_info[$user_id]['login']."</td></tr>";
echo "<tr><td>IP</th><td>".$admin_info[$user_id]['ip']."</td></tr>";
echo "<tr><td>"._ROLE."</th><td>".$info['role']."</td></tr>";
echo "<tr><td>Root</th><td>".$info['root']."</td></tr>";
echo "<tr><td>"._USER_OWNER."</th><td>".$info['owner']."</td></tr>";
echo "<tr><td>"._GET_MAIL."</th><td>".$info['get_mail']."</td></tr>";
echo "<tr><td>"._GET_PROMO_MAIL."</th><td>".$info['promo_mail']."</td></tr>";
echo "<tr><td>"._AUTOMONEY."</th><td>".$info['auto_money']."</td></tr>";
echo "<tr><td>"._ALLOW_REFERAL."</th><td>".$info['ref_active']."</td></tr>";
echo "<tr><td>"._BALANCE."</th><td>".$admin_info[$user_id]['summa']."$ (".$summa_proc."%)</td></tr>";
echo "<tr><td>"._ALLMONEY."</th><td>".$admin_info[$user_id]['allmoney']."$</td></tr>";
echo "<tr><td>"._MONEY."</th><td>".$admin_info[$user_id]['got_money']."$ (".$got_money."%)</td></tr>";
echo "<tr><td>"._MONEYIN."</th><td>"._TODAY.": <strong>".$got_today."$ </strong> (".$got_diff."$)<br>"._YESTERDAY.": <strong>".$got_yesterday."$</strong><br>"._WEEK.": <strong>".$got_week."$</strong> <span title='"._WEEK_DIFF."'>(".$got_diff_week."$)</span></td></tr>";

echo "</tbody>
</table>";