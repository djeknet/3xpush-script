<?php

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/stat.php");
require_once("../../include/info.php");
require_once('../../include/SxGeo.php');
require_once("../forms.php");
$SxGeo = new SxGeo('../../include/SxGeoMax.dat');
header('Content-Type: text/html; charset=utf-8');

error_reporting(0);
ini_set('display_errors', 0);

$check_login = check_login();
if ($check_login==false) {
exit;
}

//var_dump($_FILES);
$ip = getenv("REMOTE_ADDR");
if ($ip=='127.0.0.1') {
$ip = '213.87.248.140';
}
$result = $SxGeo->getCityFull($ip);
$geoip = geoip_new($ip);

$settings = settings("AND admin_id=".$check_login['getid']."");
$lang = $settings['lang'];

if (!$lang) $lang = 'en';
include("../langs/".$lang.".php");
$where='';
$time = thetime();

if (!empty($_POST)) {


$title = text_filter($_REQUEST['title']);
$text = text_filter($_REQUEST['text']);
$url = text_filter($_REQUEST['url']);
$sending = intval($_REQUEST['sending']);

if (!$title) {
$need[] = _MYADV4;
}
if (!$text) {
$need[] = _MYADV5;
}
if (!$url) {
$need[] = _ADDSITETEXT2;
}

if ($need) {
foreach ($need as $key => $value) {
$err.= "- ".$value."<br>";
}
status(_FORGET_TARGETS.":<br>".$err, 'warning');  
}  

$where_arr['admin_id'] = $check_login['getid'];
$where_arr['exchange']=1;
$total = check_subscribers($_POST, $where_arr);
if (!$total) $total=0;

status (_SUBSCRIBERSALL.": <b>".$total."</b>", 'info');    


$edit = intval($_REQUEST['edit']);
$copy_icon = text_filter($_REQUEST['copy_icon'], 2);
$copy_image = text_filter($_REQUEST['copy_image'], 2);

$title = str_replace("[COUNTRY]", $geoip['country'], $title);
$text = str_replace("[COUNTRY]", $geoip['country'], $text);
$title = str_replace("[REGION]", $geoip['region'], $title);
$text = str_replace("[REGION]", $geoip['region'], $text);
$title = str_replace("[CITY]", $geoip['city'], $title);
$text = str_replace("[CITY]", $geoip['city'], $text);


$brands = array('Samsung' => 'GALAXY J3 (2017)', 'Xiaomi' => 'Redmi 5', 'Huawei' => 'P smart', 'Lenovo' => 'K8', 'LG' => 'K10');

$rand=rand(0,4);
$i=0;
foreach ($brands as $key => $value) {  
if ($i!=$rand)  {$i++; continue;}   
$title = str_replace("[BRAND]", $key, $title);
$text = str_replace("[BRAND]", $key, $text);
$title = str_replace("[MODEL]", $value, $title);
$text = str_replace("[MODEL]", $value, $text);
break;       
}


if (intval($_FILES['img']['size'])) $img1_ok=1;
if (intval($_FILES['img2']['size'])) $img2_ok=1;

if ($edit) {
list($small_img, $image) = $db->sql_fetchrow($db->sql_query("SELECT icon, image FROM myads WHERE id='$edit' AND admin_id=".$check_login['getid'].""));
}
  if($copy_icon) {
      $small_img = $copy_icon;  
    }
    if($copy_image) {
      $image = $copy_image;  
    }
    
if ($img1_ok==1 || $img2_ok==1) {
$type = explode(".", $_FILES['img']['name']);
$type = strtolower(end($type));
$directory = "../../pushimg/".$check_login['getid']."";
$url_directory = "../pushimg/".$check_login['getid']."";
$imgtype = "jpg,jpeg,png";
if (!is_dir($directory)) {
mkdir($directory, 0777);
$fp = fopen ("$tizdirectory/index.html", "w");
fwrite($fp,'access dinied');
fclose($fp);
}


foreach ($_FILES as $key => $val) {
if ($val['size']==0) continue;
$filename = upload($directory, $imgtype, 800000, "", 2000, 2000, $key);
$img = "".$directory."/".$filename."";
if ($key=='img') {
resize($img, 192, 192, 1, 0);
$small_img = $url_directory."/".$filename;
} else {
resize($img, 360, 240, 1, 0);
$image = $url_directory."/".$filename;
}
}
}
if (!$small_img) {$small_img = "images/noimg.png"; $nopush=1;
} else {
$push_icon = str_replace("../", "/", $small_img);
$push_icon = "https://".$settings['siteurl'].$push_icon;
}

if ($image) {
$push_image = str_replace("../", "/", $image);
$push_image = "https://".$settings['siteurl'].$push_image;

$image = "<img src=$image border=0 class=\"preview_bigimg\">";
}

if ($url) {
$info = parse_url($url);
} else $info = array();

if (!empty($_REQUEST['options'])){
foreach ($_POST['options'] as $key => $value) {
$options[$key] = text_filter($value);
}
if ($options['button1']) {
$button1 = "<div class=preview_button>".$options['button1']."</div>";   
}
if ($options['button2']) {
$button2 = "<div class=preview_button>".$options['button2']."</div>";   
}
} else $options = array();

if ($button1 || $button2) {
$mobile_buttons = "<div class=\"preview_buttons\">".$button1."".$button2."</div>";
}
if (!$title) {$title = "<em>no text</em>"; $nopush=1;}
if (!$text) {$text = "<em>no text</em>"; $nopush=1;}

if ($nopush!=1 && intval($_POST['send_push'])==1) {
$admin_push = subscribers("AND uid=".$check_login['getid']." AND del=0");

$ads['title'] = $title;
$ads['body'] = $text;
$ads['icon'] = $push_icon;
$ads['url'] = $url;
$ads['image'] = $push_image;
if (!empty($_REQUEST['options'])) $ads['options'] = array('button1' => $options['button1'], 'button2' => $options['button2']);
$site['sid'] = 184;
$site['subid'] = '';
$site['subs_id'] = '';
$site['params'] = 0;

if (is_array($admin_push)) {
foreach ($admin_push as $key => $value) { 
$send = send_push($ads, $site, $value['token'], 1);
if (preg_match('/NotRegistered/i', $send) == 1) {
$db->sql_query("UPDATE subscribers SET del=1, last_update=now() WHERE id='".$key."'");       
} 
}
}
}
?>
<div class="preview_chrome">
<div class="preview_title">
<img src="images/browser/Chrome.png" border="0" width="16" height="16" /> Google Chrome
</div>
<div class="preview_block">
<img src="<?php echo $small_img; ?>" class="preview_img" align=left><span class="preview_text"> <b><?php echo $title; ?></b><br />
<?php echo $text; ?><br />
<span class="preview_domain"><?php echo $info['host']; ?></span>
</span>
<?php echo $image; ?>
</div>
<?php echo $button1; ?>
<?php echo $button2; ?>
</div>

<div class="preview_mobile">
<div class="preview_title">
<img src="images/browser/Chrome Mobile.png" border="0" width="16" height="16" /> Chrome Mobile
</div>
<div class="preview_block">
<img src="<?php echo $small_img; ?>" class="preview_img" width="268" align=right><span class="preview_text"> <?php echo $title; ?><br />
<span class="preview_text_2"><?php echo $text; ?></span><br />
</span>
<?php echo $image; ?>
</div>
<?php echo $mobile_buttons; ?>
</div>
<?php      
}
