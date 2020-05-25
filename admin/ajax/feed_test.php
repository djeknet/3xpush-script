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
$subs_id = intval($params[0]);
                             
echo ""._FEEDS_TEXT2." <input type=\"text\" value=\"$subs_id\" size=\"10\" name=\"subs_id\" onchange=\"aj('feed_test.php',this.value,3); return false;\" /><hr>";

if ($subs_id) {
  $subs_info = subscribers("AND id=".$subs_id."");  
  if (is_array($subs_info)) {
  $subs_info = $subs_info[0];
  $get_feed_ads = get_feed_ads($subs_info);    
  $feeds = feeds();
  echo "<strong>"._FEEDS_TEXT3."</strong><br>
  <b>"._REGION.":</b> ".$subs_info['cc']." (".$subs_info['ip'].")<br>
  <b>"._FEEDS_TEXT4.":</b> ".$subs_info['os'].", ".$subs_info['browser_short']." [".$subs_info['lang']."]<br>
  <b>"._DEVICE.":</b> ".$subs_info['device'].", ".$subs_info['brand'].", ".$subs_info['model']."<br>
  <b>"._FEEDS_TEXT5.":</b> ".$subs_info['sended'].", ".$subs_info['views'].", ".$subs_info['clicks']."<hr>
  <strong>"._FEEDS_TEXT6."</strong><br>";
  
  if (is_array($get_feed_ads['ads'])) {
       echo '<table>';
 echo "<tr><td width=20 valign=top>№</td>
       <td  width=50 valign=top>"._FEED."</td>
       <td  width=80 valign=top>"._ADVIMAGE."</td>
       <td  width=50% valign=top>"._TITLE."</td>
       <td valign=top>BID</td>
       </tr>";  
    foreach ($get_feed_ads['ads'] as $key => $value) {
        $feed_name = $feeds[$value['feed_id']]['name'];
       echo "<tr><td valign=top>$key</td>
       <td  valign=top>$feed_name</td>
       <td  valign=top><img src=".$value['icon']." align=left width=70></td>
       <td  valign=top>".$value['title']."</td>
       <td valign=top>".$value['bid']."</td>
       </tr>
";
    }
    echo '</table>';
  } else {
  status(_FEEDS_TEXT7, 'warning');    
  print_r($get_feed_ads['info']);
  }
  
  } else {
  status(_FEEDS_TEXT8, 'warning');  
  }
  
}