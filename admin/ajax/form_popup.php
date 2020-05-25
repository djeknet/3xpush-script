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
if ($check_login == false) {
    exit;
}

$settings = settings("AND admin_id=" . $check_login['getid'] . "");

$lang = $settings['lang'];
include("../langs/" . $lang . ".php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'load_form';
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$id = intval($id);

if ($check_login['root']!=1) {
    $where_admin = "AND admin_id=".$check_login['getid']."";
    }  
    
if ($action == 'popup_cpv_price') {

    $data = myads('and id=' . $id.' AND admin_id='.$check_login['getid'].'');
    $data = $data[$id];

    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<input type="hidden" name="action" id="action" value="popup_cpv_price_update">';
    echo '<table class="form_popup_table">';
    echo '<tr>';
    echo '<td>'._CPVPRICE.':</td>';
    echo '<td><input type="text" id="cpc_price" name="cpc_price" value="' . $data['cpv_price'] . '" placeholder="'._CPVPRICE.'" data-min="' . $settings['cpv_minprice'] . '" data-max="' . $settings['cpv_maxprice'] . '"><span>$</span></td>';
    echo '</tr>';
    echo '</table>';
}

if($action == 'popup_cpv_price_update') {

    $cpv_price = isset($_REQUEST['cpc_price']) ? $_REQUEST['cpc_price'] : 0;
 
    if ($settings['cpv_minprice'] > $cpv_price) $stop = _AOPTIONS6." ".$settings['cpv_minprice'].". ";
    if ($settings['cpv_maxprice'] < $cpv_price) $stop = _AOPTIONS7." ".$settings['cpv_maxprice'].". ";
    if (!$stop) {
    $db->sql_query('UPDATE myads set cpv_price="' . text_filter($cpv_price) . '" where id="' . $id . '" '.$where_admin.'');
    jset($check_login['id'], _PRICE_CHANGE.": $cpv_price");
    if ($check_login['id']!=$check_login['getid']) {
     alert(_PRICE_CHANGE." $id: $cpv_price (user: ".$check_login['login'].")", $check_login['getid']);
    }
    echo 'OK';
    } else {
    echo $stop;   
    }

}

if ($action == 'popup_cpc_price') {

    $data = myads('and id=' . $id.' AND admin_id='.$check_login['getid'].'');
    $data = $data[$id];
    if (!$data['min_cpc_price']) $data['min_cpc_price'] = $settings['min_cpc_price'];

    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<input type="hidden" name="action" id="action" value="popup_cpc_price_update">';
    echo '<table class="form_popup_table">';
    echo '<tr>';
    echo '<td>'._CPCPRICE.':</td>';
    echo '<td><input type="text" id="cpc_price" name="cpc_price" value="' . $data['cpc_price'] . '" placeholder="'._CPCPRICE.'" data-min="' . $data['min_cpc_price'] . '" data-max="' . $settings['max_cpc_price'] . '"><span>$</span></td>';
    echo '</tr>';
    echo '</table>';
}

if($action == 'popup_cpc_price_update') {

    $cpc_price = isset($_REQUEST['cpc_price']) ? $_REQUEST['cpc_price'] : 0;
    if ($settings['min_cpc_price'] > $cpc_price) $stop = _CPCPRICE_ERROR1." ".$settings['min_cpc_price'].". ";
    if ($settings['max_cpc_price'] < $cpc_price) $stop = _CPCPRICE_ERROR2." ".$settings['max_cpc_price'].". ";
    if (!$stop) {
    $db->sql_query('UPDATE myads set cpc_price="' . text_filter($cpc_price) . '" where id="' . $id . '" '.$where_admin.'');
    jset($check_login['id'], _PRICE_CHANGE.": $cpc_price");
    if ($check_login['id']!=$check_login['getid']) {
     alert(_PRICE_CHANGE." $id: $cpc_price (user: ".$check_login['login'].")", $check_login['getid']);
    }
    echo 'OK';
    } else {
    echo $stop;   
    }

}

if ($action == 'group_title') {

    $data = groups('and id=' . $id.' AND admin_id='.$check_login['getid'].'');
    $data = $data[$id];

    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<input type="hidden" name="action" id="action" value="popup_group_title_update">';
    echo '<table class="form_popup_table">';
    echo '<tr>';
    echo '<td>'._GROUP_NAME.':</td>';
    echo '<td><input type="text" id="group_title" name="group_title" value="' . $data['title'] . '" placeholder="'._GROUP_NAME.'"></td>';
    echo '</tr>';
    echo '</table>';
}
if($action == 'popup_group_title_update') {

    $group_title = isset($_REQUEST['group_title']) ? $_REQUEST['group_title'] : '';
    if (!$group_title) $stop = _GROUP_ERR;
    if (!$stop) {
    $db->sql_query('UPDATE myads_groups set title="' . text_filter($group_title) . '" where id="' . $id . '" '.$where_admin.'');
    jset($check_login['id'], _GROUP_NAME.": $group_title");
    if ($check_login['id']!=$check_login['getid']) {
     alert(_GROUP_NAME." $id: $group_title (user: ".$check_login['login'].")", $check_login['getid']);
    }
    echo 'OK';
    } else {
    echo $stop;   
    }

}