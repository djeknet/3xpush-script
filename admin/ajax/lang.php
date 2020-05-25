<?php
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

require_once("../../include/mysql.php");
require_once("../../include/func.php");

require_once '../models/LangsModel.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$name_array = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$lang_array = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : '';
$text_array = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';

$data = array(
    'success' => true,
    'messages' => 'OK'
);

if ($action == 'check_macros') {

    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';

    $result = LangsModel::getInstance()->check_macros($name);

    if($result) {
        $data['success'] = false;
        $data['messages'] = 'A macro with the same name already exists..';
    }

}

if($action == 'search') {

    $macros = isset($_REQUEST['search_macros']) ? $_REQUEST['search_macros'] : '';
    $text = isset($_REQUEST['search_text']) ? $_REQUEST['search_text'] : '';

    $ids = LangsModel::getInstance()->search($macros, $text);
    $data['messages'] = LangsModel::getInstance()->getMacros($ids);
    $data['success'] = true;
}

if(!$action) {

    foreach ($name_array as $index => $name) {

        LangsModel::getInstance()->deleteByName($name);

        $count = count($lang_array[$index]);

        for ($i = 0; $i < $count; $i++) {
            $lang = $lang_array[$index][$i];
            $text = $text_array[$index][$i];
            $params = [
                'name' => $name,
                'lang' => $lang,
                'text' => $text
            ];
            LangsModel::getInstance()->save($params);
        }
    }
}

echo json_encode($data);
