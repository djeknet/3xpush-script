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
$type = intval($params[0]);
$id = intval($params[1]);

$site_info=array();
$today  = date("Y-m-d");

// добавление и редактирование предложения
if ($type==1) {
if ($id) {
    $traf_exchange = traf_exchange("AND id=$id AND status=1"); 
    if (!is_array($traf_exchange)) {
        echo 'error id';
        exit;
    } 
    $traf_exchange = $traf_exchange[$id];

    
    $site_info = sites("AND id=".$traf_exchange['site_id']." AND admin_id=".$check_login['getid']."");    
     if ($traf_exchange['max_send_changed']==$today) $block=1;
           
} else {
    $traf_exchange_admin = traf_exchange("AND admin_id=".$check_login['getid'].""); 
    if(is_array($traf_exchange_admin)) {
    foreach ($traf_exchange_admin as $key => $value) {
       $sids[] = $value['site_id'];
    }
    $sids_str = implode(',', $sids);
    $where = "AND id NOT IN (".$sids_str.")";
    }
    $sites = sites("AND admin_id=".$check_login['getid']." AND type=1 AND `subscribers`-`unsubs` >= ".$settings['exchange_min']." ".$where);   
}

status(_TRAF_EXCH_INFO.": ".$settings['exchange_min'], 'info');  
                              
echo "<table width=100%><tbody>";           
if (!$id) {
echo "<tr><td valign=top width=200>"._TRAF_EXCH_SITES."</th><td>";
if (is_array($sites)) {
    echo "<select name=\"sid\" class=\"form-control-sm form-control col col-md-8\">";
foreach ($sites as $keyid => $value) {
    $subs = $value['subscribers'] - $value['unsubs'];
    echo "<option value=\"$keyid\">".$value['url']." (".$subs.")</option>";
    }
echo "</select>";    
    } else {
        status(_TRAF_EXCH_NO_SITES, 'warning');
    }
    
echo "</td></tr>";
}
echo "<tr><td valign=top>"._MAXSEND."</th><td>";
if ($block!=1) {   
echo "<input name=\"max_send\" type=\"text\" placeholder=\">1000\" class=\"form-control-sm form-control col col-md-8\" value=\"".$traf_exchange['max_send']."\"><br> "._TRAF_EXCH_MAXSEND_TOOLTIP."";  
} else {
    status(_TRAF_MAX_SEND_WARN, 'warning');
}
echo "</td></tr></tbody>
</table>
<input name=\"edit\" type=\"hidden\" value=\"".$id."\">
<input name=\"offer\" type=\"hidden\" value=\"1\">";

}

// отправка заявки
if ($type==2 && $id) {
    $traf_exchange_admin = traf_exchange_admins("AND admin_id=".$check_login['getid'].""); 
    if(is_array($traf_exchange_admin)) {
    foreach ($traf_exchange_admin as $key => $value) {
       $sids[] = $value['admin_site'];
    }
    $sids_str = implode(',', $sids);
    $where = "AND id NOT IN (".$sids_str.")";
    }
    $sites = sites("AND admin_id=".$check_login['getid']." AND type=1 AND `subscribers`-`unsubs` >= 100 ".$where);    
    
    
status(_TRAF_SEND_REQUEST_INFO, 'info');  
                              
echo "<table width=100%><tbody>";           
echo "<tr><td valign=top width=200>"._TRAF_EXCH_SITES."</th><td>";
if (is_array($sites)) {
    echo "<select name=\"sid\" class=\"form-control-sm form-control col col-md-8\">";
foreach ($sites as $keyid => $value) {
    $subs = $value['subscribers'] - $value['unsubs'];
    echo "<option value=\"$keyid\">".$value['url']." (".$subs.")</option>";
    }
echo "</select>";    
    } else {
        status(_TRAF_EXCH_NO_SITES, 'warning');
    }
    
echo "</td></tr>
</tbody>
</table>
<input name=\"new_request\" type=\"hidden\" value=\"1\">
<input name=\"traf_id\" type=\"hidden\" value=\"$id\">";

    }