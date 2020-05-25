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

if ($check_login['id']) {
   status('You are already logged in', 'warning'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo _GET_NEW_PASS; ?> - <?php echo $settings['sitename']; ?></title>
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

$email = text_filter($_POST['email']);
$code = text_filter($_REQUEST['c']);

$newpass = text_filter($_POST['newpass']);
$newpass2 = text_filter($_POST['newpass2']);

// отправка ссылка с кодом
if ($email) {
    $ip = getenv("REMOTE_ADDR");
    $cache_code = md5('newpass'.$ip);
    if ($config['memcache_ip']) $count = $memcached->get($cache_code);
    if (!$count) $count = 0;
    $count = $count + 1;
    if ($count >= 3) $stop = "<br>"._NEW_PASS_ERR;
     if ($settings['captcha_login']==1) { 
$recaptcha = recaptcha();
if ($recaptcha!=1)  $stop .= _CAPTCHAERROR." <br>";
}

    $is_user = get_onerow('id', 'admins', "email='$email' AND status=1");
    if ($is_user && !$stop) {
        $hash = gen_pass(50);
        $admin_settings = settings("AND admin_id='".$is_user."'");
        $db->sql_query("UPDATE admins SET new_pass_code='$hash' WHERE id='".$is_user."'");
        
        $link = "<br><br><a href=https://".$settings['siteurl']."/admin/index.php?m=forget&c=$hash>https://".$settings['siteurl']."/index.php?m=forget&c=$hash</a>";
        
        if ($admin_settings['lang']=='ru') {
          $title = "Восстановление доступа";  
          $content = "Если вы отправляли запрос на изменение пароля, то перейдите по ссылке ниже и укажите новый пароль: ".$link."";
        } else {
          $title = "Access recovery";  
          $content = "If you sent a request to change the password, then follow the link below and specify a new password: ".$link."";  
        }
       $sended =	mail_send($email, $settings['from_mail'], $title, $content, $is_user, $admin_settings['lang']);
       if ($config['memcache_ip']) {
     $memcached->set($cache_code, $count, false, time() + 86000);       
        }
       status(_GET_NEW_PASS_MAIL, 'success'); 
    } else {
       status(_USER_NOTFOUND.' '.$stop, 'danger');
    }
}
// проверка кода, если все верно, то выводим форму для изменения пароля
if ($code) {
   $is_code = get_onerow('id', 'admins', "new_pass_code='$code' AND status=1");    
   if (!$is_code) {
    status(_CODE_ERROR, 'danger');
   } elseif($is_code && $newpass && $newpass2) {
      if ($newpass==$newpass2) {
        $newpass=md5($newpass);
      $db->sql_query("UPDATE admins SET pass='$newpass', new_pass_code='' WHERE id='".$is_code."'");  
   status(_PASS_CHANGED, 'success');
   redir("index.php?m=login");     
      } else {
    status(_NEWPASSWRONG, 'danger');     
      }
   } 
   
}

?>

<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
        <div align=center><img src="images/logo-blue.png" width=200 border=0></div>
			<div class="login-form">
                  <form action="index.php?m=forget" method="post">
                   <?php
                  if ($is_code) {
                 ?> 
                <div class="form-group form-floating-label">
					<input  id="newpass" name="newpass" type="text" value="" class="form-control input-border-bottom" required>
					<label for="newpass" class="placeholder"><?php echo _NEWPASS; ?>*</label>
				</div>
				<div class="form-group form-floating-label">
					<input  id="newpass2" name="newpass2" type="password" class="form-control input-border-bottom" required>
					<label for="newpass2" class="placeholder"><?php echo _REPEAT; ?></label>
				</div>
                   <?php   
                  } else {
                   ?>
                <strong><?php echo _GET_NEW_PASS; ?></strong>              
                        <div class="form-group">
                            <input type="text" name="email" value="" class="form-control" placeholder="e-mail">
                        </div>
                   <?php
                  }
                   ?>   
				<div class="row form-sub m-0">

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
                    <input type="hidden" name="c" value="<?php echo $code; ?>" />
                                <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30"><?php echo _ENTER; ?></button>
                 <br><br><div align=center><a href=index.php?m=login><i class="menu-icon fa fa-key"></i> <?php echo _LOGININ; ?></a></div>               
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