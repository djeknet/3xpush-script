<?php

ini_set('display_errors', 0);
error_reporting(0);
require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/stat.php");
require_once("../../include/info.php");
require_once("../forms.php");
header('Content-Type: text/html; charset=utf-8');

$dev = intval($_GET['dev']);
if (!$dev) $dev = 0;
$check_login = check_login();
if ($check_login==false) {
  exit;
}
$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];
include("../langs/".$lang.".php");
$today = date("Y-m-d");
$param = text_filter($_GET['param']);
$params = explode('|', $param);
$sid = intval($params[0]);

$traf_exchange_stat = traf_exchange_stat("AND site_id=".$sid."", '', 7) ;

 
if ($dev==1) {
echo 'stat: <br>';
print_r($traf_exchange_stat);
echo '<hr>';
}

if (is_array($traf_exchange_stat)) {

status(_TRAF_EXCH_STAT_INFO, 'info');   

echo "<table width=100% class=\"table\"><tbody>";
echo "<tr>
<td>"._DATE."</td>
<td>"._SEND_PUSH."</td>
</tr>";


foreach ($traf_exchange_stat as $key => $value) {
if ($key=='ALL') continue;
if ($key==$today) $key = _TODAY;
echo "<tr>
<td>".$key."</td>
<td>".$value['sended']."</td>
</tr>";
}

echo "<tr>
<td><strong>"._ALL."</strong></td>
<td>".$traf_exchange_stat['ALL']['sended']."</td>
</tr>";

echo "</tbody>
</table>";    
    } else {
      status(_NOTHINGFOUND, 'info');      
    }