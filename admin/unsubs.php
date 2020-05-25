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

require_once("../include/mysql.php");
require_once("../include/func.php");
require_once("../include/info.php");
require_once("forms.php");
$admin_id = intval($_GET['id']);
$key = text_filter($_GET['key']);
$promo = intval($_GET['promo']); // 1 - unsubscribe only from promotional mailings

if (!$admin_id || !$key) exit;

$code = md5($config['global_secret'].$admin_id); 
if ($code!=$key) exit;

if ($promo) {
$db->sql_query("UPDATE admins SET promo_mail=0 WHERE id='$admin_id'");   
} else {
$db->sql_query("UPDATE admins SET get_mail=0 WHERE id='$admin_id'");
}

$db->sql_query('INSERT INTO mails_stat (date, unsubs) VALUES (CURRENT_DATE(), 1) ON DUPLICATE KEY UPDATE  unsubs = unsubs + 1');

$lang = get_lang();
if ($lang!='ru')  $lang='en';
include("langs/".$lang.".php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo $settings['sitename']; ?></title>
    <meta name="description" content="<?php echo _DESCRIPTION; ?>">
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="apple-touch-icon" href="images/apple-icon.png">
    <link rel="icon" href="../favicon.png" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['../assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>
	
	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/atlantis.css">
</head>
<body class="login">

<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
        <div align=center><img src="images/logo-blue.png" width=200 border=0></div>
			<div class="login-form">
               <div class="login-content">
           <div align=center> <img src="images/logo.png" width=200 border=0></div>
                <div class="login-form">
               <?php status(_NOMAIL, 'success'); ?>
              <p align="center"> <a href="index.php?m=login"><?php echo _ENTER; ?></a></p>
                </div>
            </div>

			</div>
		</div>
	</div>
	<script src="assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/atlantis.min.js"></script>


 <?php
 echo content_name('metriks', 'code');   
 ?>               
</body>
</html>