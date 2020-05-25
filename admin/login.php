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

if(count(get_included_files()) ==1) exit("Direct access not permitted.");

$ip_arr = geoip_new($ip);

if (isset($_POST['login']) && isset($_POST['pass'])) {
    if ($settings['black_ip']) {
   $settings['black_ip'] = explode(",", $settings['black_ip']); 
   if (in_array($ip, $settings['black_ip'])) {
    $blocked_ip=1;
   }
 }
 
 if ($blocked_ip!=1) {
    
 if ($settings['captcha_login']==1) { 
$recaptcha = recaptcha();
if ($recaptcha!=1)  $stop .= _CAPTCHAERROR." <br>";
}
   if (!$stop) {
 $redirect = text_filter($_POST['redirect'], 2);
 $check_admin = check_admin($_POST['login'], $_POST['pass'], $_POST['rememberme']);

 if ($check_admin==1) {
    if ($redirect) $link = "index.php?".$redirect; else $link = "index.php";

 header("Location: ".$link."");
 exit;
 
 }  elseif ($check_admin==2) {  // email не подтвержден
   $email_not_confirmed=1;

 }  elseif ($check_admin==3) {   // вход с нового места

   $stop = _NEW_CITY_ENTER;
   
 } else $error=1;
 }
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo _LOGININ; ?> - <?php echo $settings['sitename']; ?></title>
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

       <?php

         if ($error==1) {
           status(_LOGIN1, 'danger');
         }
         if ($email_not_confirmed==1) {
           status(_CONFIRM_MAIL_ERR, 'danger');
         }
         if ($blocked_ip==1) {
          status(_IP_BLOCKED, 'danger');   
         }
        if (intval($_GET['success'])==1) {
        status(_REGISTEROK, 'success');
        }
        if (intval($_GET['email_check'])==1) {
        status(_EMAIL_NEED_CONFIRM, 'success');
        }
        
// подтверждение почты
$check_mail = text_filter($_GET['check_mail']);
$mail_code = text_filter($_GET['check_code']);
$uid = intval($_GET['uid']);

if ($check_mail && $mail_code && $uid) {
  $check_code = md5($uid.$check_mail.$config['global_secret']);
if ($check_code!=$mail_code) {
  status('error code', 'danger');
} else {
$db->sql_query('UPDATE admins SET email_check=1 WHERE id='.$uid.'');
jset($check_login['id'], _MAILAPPROVER.": ".$check_login['email'].""); 
status(_MAILAPPROVER.". "._MAY_LOGIN, 'success'); 
} 
}

// подтверждение города
$new_city = text_filter($_GET['new_city']);
$city_code = text_filter($_GET['check_code']);
$uid = intval($_GET['uid']);

if ($new_city && $city_code && $uid) {
$new_city = base64_decode($new_city);
  $check_code = md5($uid.$new_city.$config['global_secret']);
if ($check_code!=$city_code) {
  status('error code', 'danger');
} elseif ($ip_arr['city']!=$new_city) {
  status('wrong city, please relogin', 'danger');
} else {
$db->sql_query('UPDATE admins SET city="'.$ip_arr['city'].'", cc="'.$ip_arr['cc'].'" WHERE id='.$uid.'');
jset($check_login['id'], _NEW_CITY_APPROVED); 

status(_NEW_CITY_APPROVED.". "._MAY_LOGIN, 'success'); 
} 
}


       ?>

<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
        <div align=center><img src="images/logo-blue.png" width=200 border=0></div>
			<h3 class="text-center"><?php echo _LOGININ; ?></h3>
			<div class="login-form">
               <?php
                 if ($stop) {
                  status($stop, 'danger');   
                   }
                 if ($info) {
                  status($info, 'info');   
                   }  
                ?>
                  <form action="index.php?m=login" method="post">
                <div class="form-group form-floating-label">
					<input  id="login" name="login" type="text" value="<?php echo text_filter($_POST['login']); ?>" class="form-control input-border-bottom" required>
					<label for="login" class="placeholder"><?php echo _LOGIN; ?>*</label>
				</div>
				<div class="form-group form-floating-label">
					<input  id="passwordsignin" name="pass" type="password" class="form-control input-border-bottom" required>
					<label for="passwordsignin" class="placeholder"><?php echo _PASS; ?></label>
					<div class="show-password">
						<i class="icon-eye"></i>
					</div>
				</div>
				<div class="row form-sub m-0">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="rememberme" id="rememberme" >
						<label class="custom-control-label" for="rememberme"><?php echo _REMEMBERME; ?></label>
					</div>

                            <?php
                           if ($settings['captcha_login']==1) { 
                            ?>
                        <script src='https://www.google.com/recaptcha/api.js'></script>
<div class="g-recaptcha" data-sitekey=" <?php echo $settings['google_recaptcha_public']?>"></div>
                        <?php
                         }
                            ?>
				</div>
				<div class="form-action">
                    <input type="hidden" name="redirect" value="<?php echo $_SERVER["QUERY_STRING"]; ?>" />
                                <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30"><?php echo _ENTER; ?></button>
                 <br><br><div align=center><a href=index.php?m=register><i class="menu-icon fa fa-key"></i> <?php echo _REGISTER; ?></a></div>
                          <div align=center><a href=index.php?m=forget><i class="menu-icon fa fa-lock"></i> <?php echo _FORGET_PASS; ?></a></div>               
				</div>
                </form>

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