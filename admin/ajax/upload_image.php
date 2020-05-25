<?php

ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: text/html; charset=utf-8');

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/info.php");

$check_login = check_login();
if ($check_login==false) {
  exit;
}
$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];
include("../langs/".$lang.".php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'upload';
$landingId = isset($_REQUEST['landingId']) ? $_REQUEST['landingId'] : '';

if($action == 'upload') {

    $file = isset($_FILES['image']) ? $_FILES['image'] : null;

    $allowedExt = array('jpeg', 'jpg', 'png', 'gif');

    if (!is_null($file)) {

        $uploaddir = 'landings/' . $landingId . '/data';
        $uploaddir_full = $root . $uploaddir;

        $file_name = $file['name'];
        $ext = pathinfo("$uploaddir_full/$file_name", PATHINFO_EXTENSION);
        $file_name = md5("$uploaddir_full/$file_name") . '.' . $ext;

        if($file['size'] > 1024 * 1024) {
            die(json_encode(array('error' => _MAXFILESIZE.' 1MB.')));
        }

        if(!in_array($ext, $allowedExt)) {
            die(json_encode(array('error' => sprintf(_FILEFORTAMTS.' %s.', implode(', ', $allowedExt)))));
        }

        if (!is_dir($uploaddir_full)) mkdir($uploaddir_full, 0777, true);

        $done_files = array();

        if (move_uploaded_file($file['tmp_name'], "$uploaddir_full/$file_name")) {
            $done_files = realpath("$uploaddir_full/$file_name");
            $done_files = "/$uploaddir/$file_name";
        }

        $data = $done_files ? array('file' => $done_files) : array('error' => _LOADERROR);
        die(json_encode($data));
    }

    die(json_encode(array('error' => _LOADERROR)));

}

if($action == 'remove') {

    $file_name = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
    $server_file = $uploaddir_full = $root . $file_name;

    $result = false;

    if(file_exists($server_file)) {
        $result = unlink($server_file);
    }

    die(json_encode(array('success' => $result)));
}




