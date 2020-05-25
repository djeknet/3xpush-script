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


define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
error_reporting(0);
ini_set('display_errors', 0);

require_once("../include/mysql.php");
require_once("../include/func.php");
require_once("../include/info.php");
include_once 'library/PageParser.php';


$check_login = check_login();
if ($check_login['role'] != 1) {
  exit;
}

$type=intval($_GET['type']);

function url_exists($url)
{
    if (!$fp = curl_init($url)) return false;
    return true;
}

function copyPage($type)
{
    global $db;
    $errors = array();

    $landingId = intval($_REQUEST['landingId']);
    if (!$landingId) $landingId = rand(1111,99999999);
    $pageUrl = isset($_REQUEST['pageUrl']) ? $_REQUEST['pageUrl'] : '';
    $removeFiles = isset($_REQUEST['removeFiles']) ? intval($_REQUEST['removeFiles']) : '';

    // путь к файлам
    $scheme = isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] :
        (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 443 == $_SERVER['SERVER_PORT']) ? 'https' : 'http');
    $siteUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
//    $siteUrl = '/admin';

    if(!url_exists($pageUrl)) {
        $errors[] = 'Incorrect URL';
    }

    if (!empty($_POST['proxy']) && !preg_match('/^((([0-9]{1,3})(\.?)){4})\:([0-9]{1,5})$/', $_POST['proxy'])) {
        $errors[] = 'Incorrect Proxy';
    }

    if (empty($errors)) {

        $replaces = array();
        $parser = new PageParser($pageUrl, $replaces, $siteUrl);
        $parser->setPageId($landingId);

        if (!empty($_POST['proxy'])) {
            $parser->useProxy($_POST['proxy']);
        }

        $parser->setRemoveFiles($removeFiles);
        $result = $parser->pageCopyAndGetId();
        $pageId = null;

        if ($result['status'] == 'bad') {
            $errors = $result['errors'];
            $errors[] = 'Failed to copy this page, it may not be available or not found.';
        } else {

            $html = file_get_contents($parser->getPathFull());
            $html = iconv('windows-1251', 'utf-8', $html);
            //error_log(print_r($html, 1));
            $html = mysqli_real_escape_string($db->db_connect_id, $html);
            if (!$type) {
            $resultSQL = $db->sql_query(sprintf("UPDATE sites SET html='%s' where id='%s' limit 1;", $html, $landingId));
            } else {
 $db->sql_query("INSERT INTO temp_table (name, value)
VALUES ('copypage', '".$html."') ON DUPLICATE KEY UPDATE
                  value = '".$html."'");
                  
            $resultSQL=1; 

            }

            if(!$resultSQL) {
                $errors[] = mysqli_error($db->db_connect_id);
                $result['status'] = 'bad';
            }

            $pageId = $result['id'];
        }

    }

    $data = array(
        'status' => isset($result['status']) ? $result['status'] : 'bad',
        'errors' => $errors,
        'pageId' => $pageId
    );

    echo json_encode($data);

}

copyPage($type);
