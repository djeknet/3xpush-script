<?php
// цены по дням, со страницы Статистика - Цены, для выбраннх категорий

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
$type = text_filter($params[0]);
$key = text_filter($params[1]);
$date_from = text_filter($params[2]);
$date_to = text_filter($params[31]);

if ($type && $key) {

if ($date_from && $date_to) {
   $where = "AND date >= '".$date_from."' AND date <= '".$date_to."'";   
}
if ($type=='regions') {
    echo "<h4>"._REGION_DAY_PRICE." ".$key."</h4>";
    $where .= "AND region='".$key."'";
    $prices = prices($where, $type, 1) ; 


 foreach ($prices['region'] as $date => $value) {
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; 
            if ($cpv_views>0) $cpc_views = $views - $cpv_views;
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            
           $data['legends'][] = $date;
           $data['title']['CPC'][] = $cpc;
           $data['title']['CPM'][] = $cpv;
      } 
      
      echo "<script src=\"../vendors/chart.js/dist/Chart.bundle.min.js\"></script>
    <script src=\"../assets/js/init-scripts/chart-js/chartjs-init.js\"></script>";
 echo chart_bar("barcode", $data);  
 } 
}