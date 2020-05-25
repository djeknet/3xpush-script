<?php
error_reporting(0);
ini_set('display_errors', false);

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/info.php");

$check_login = check_login();
if ($check_login==false) {
  exit;
}
$param = text_filter($_GET['param']);
if ($param) $params = explode('|', $param);

$id = $params[0];
if ($id) {
$where = "AND id='$id'";
$where .= "AND admin_id=".$check_login['getid']." ";
$feeds = feeds($where);

list($uid, $ip, $browser, $lang, $sid, $device, $brand, $model, $createtime, $referer) = $db->sql_fetchrow($db->sql_query("SELECT id, ip, browser, lang, sid, device, brand, model, createtime, referer FROM subscribers WHERE sended>0 AND admin_id=".$check_login['getid']." ORDER by rand() limit 1"));
if (!$lang) $lang = 'en';

  $replaces = array(
                    'SITE_ID'   => $sid,
                    'IP'        => ''.$ip.'',
                    'UID'       => $uid,
                    'SUB'       => 0,
                    'LANG'       => ''.$lang.'',
                    'DEVICE'       => ''.$device.'',
                    'BRAND'       => ''.$brand.'',
                    'MODEL'       => ''.$model.'',
                    'REF'       => ''.$referer.'',
                    'DATE'       => ''.$createtime.'',
                    'AGENT'     => urlencode($browser)
                );

                 foreach ($feeds as $key => $value) {
                 if (!$value['url']) {
                 echo "<b class=red>no url</b><br />";
                 exit;
                 }
                $value['url'] = str_replace("&amp;", "&", $value['url']);
                $feed_url_get = str_replace(array_keys($replaces), array_values($replaces), $value['url']);

                $start = microtime(1);
                 $curl_result = get_fcontent( $feed_url_get,  0, 1 , $lang);
                 if ($curl_result[1]['http_code']==200) {
                  $feed_data = json_decode($curl_result[0], true);  
                  if (empty($feed_data)) {
                  $feed_data = json_decode(file_get_contents($feed_url_get), true);  
                    if (empty($feed_data[0])) {
                 $feed_data = array($feed_data);
                 }
                  }
                 } elseif ($curl_result[1]['http_code']==400) {
                     $feed_data = json_decode(file_get_contents($feed_url_get), true);  
                    if (empty($feed_data[0])) {
                 $feed_data = array($feed_data);
                 }
                    }
                 
                 if (empty($feed_data)) {
                   echo '<b class=red>feed not available</b>';
                	exit;
                 }
                 
                $end = microtime(1);
                $alltime = $end - $start;
                $alltime = round($alltime, 3);
                 $list = count($feed_data);

                 if (!empty($feed_data['data'])) {
                 $feed_data = $feed_data['data'];
                 }

                 if (is_array($feed_data) && $list>0) {

                 $title_name = $value['feed_title'];
                 $body_name = $value['feed_body'];
                 $bid_name = $value['feed_bid'];
                 $link_name = $value['feed_link_click_action'];
                 $icon_name = $value['feed_link_icon'];
                 $image_name = $value['feed_link_image'];
                 $feed_winurl = $value['feed_winurl'];
                 $convert_rate = $value['convert_rate'];
                $testok=1;

                foreach ($feed_data as $num => $val) {


                     if ($convert_rate) {
                   $is_price = $val[$bid_name];
                   if (stripos($convert_rate, "/") !== false) {
                  $convert_rates = str_replace("/", "", $convert_rate);
                  $val[$bid_name] = $val[$bid_name] / $convert_rates;
                   }
                   else {
                  $val[$bid_name] = $val[$bid_name] * $convert_rate;
                   }
                  } else $is_price=0;

                    if ($val[$title_name]) {
                    echo "<b class=green>title: OK</b><br />";
                    } else {
                    $testok=0;
                    echo "<b class=red>title: ERROR</b><br />";
                    }
                    if ($body_name && $val[$body_name]) {
                    echo "<b class=green>body: OK</b><br />";
                    } elseif($body_name && !$val[$body_name]) {
                    echo "<b class=red>body: ERROR</b><br />";
                    }
                    if ($val[$bid_name]) {
                    echo "<b class=green>bid: OK</b><br />";
                    } else {
                    $testok=0;
                    echo "<b class=red>bid: ERROR</b><br />";
                    }
                    if ($val[$link_name]) {
                    echo "<b class=green>link: OK</b><br />";
                    } else {
                    $testok=0;
                    echo "<b class=red>link: ERROR</b><br />";
                    }
                    if ($val[$icon_name]) {
                    echo "<b class=green>icon: OK</b><br />";
                    } else {
                    $testok=0;
                    echo "<b class=red>icon: ERROR</b><br />";
                    }
                    if ($image_name && $val[$image_name]) {
                    echo "<b class=green>image: OK</b><br />";
                    } elseif($image_name) {
                    echo "<b class=red>image: ERROR</b><br />";
                    }
                    if ($convert_rate) {
                    echo "<b>new price: ".$val[$bid_name]."</b><br />";
                    }
                    if ($feed_winurl) {
                    echo "<b>winurl: ".$val[$feed_winurl]."</b><br />";
                    }
                 break;
                }
                if ($testok!=1) $test = "<b class=red>NO</b>"; else
                {
                $test = "<b class=green>OK</b>";
                $db->sql_query('UPDATE feeds SET tested=1 WHERE id='.$id.'');
                }
                echo "TEST: ".$test." <br /> time: $alltime list: $list<br />";

                } else {
                echo "<b class=red>feed empty result<br> (ip: $ip)</b><br />";
                }
 }

}