<?php
ini_set('display_errors', 0);
error_reporting(0);
require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/info.php");

$check_login = check_login();
if ($check_login['role'] != 1) {
    exit;
}

$html = isset($_REQUEST['html']) ? $_REQUEST['html'] : '';
$landingId = intval($_REQUEST['id']) ? $_REQUEST['id'] : '';

if (isset($_REQUEST['land_options'])) {
    $land_options = json_encode($_REQUEST['land_options']);
    $land_options = mysqli_real_escape_string($db->db_connect_id, $land_options);
} else {
    $land_options = '';
}

if($landingId && $land_options) {
    $sql = sprintf('update sites set land_options="%s" where id="%s" limit 1;', $land_options, $landingId);
    $db->sql_query($sql);
}

if ($landingId && $html) {

    $html = mysqli_real_escape_string($db->db_connect_id, $html);

    $sql = sprintf('update sites set html="%s" where id="%s" limit 1;', $html, $landingId);
    $db->sql_query($sql);

    echo 'ok';
    return;

}

echo 'ok';
