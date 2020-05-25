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
$from = text_filter($_COOKIE['ref']);
$referal = intval($_COOKIE['r']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo _REGISTER; ?> - <?php echo $settings['sitename']; ?></title>
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
$save = intval($_POST['save']);

if ($settings['black_ip']) {
   $settings['black_ip'] = explode(",", $settings['black_ip']); 
   if (in_array($ip, $settings['black_ip'])) {
    $blocked_ip=1;
   }
 }
 
if ($save==1 && $blocked_ip!=1) {
$name = text_filter($_POST['name']);
$login = text_filter($_POST['login']);
$email = text_filter($_POST['email']);
$pass = text_filter($_POST['pass']);
$telegram = text_filter($_POST['telegram']);
$sellang = text_filter($_POST['sellang']);
$skype = text_filter($_POST['skype']);

if ($sellang) $lang = $sellang;

if ($settings['captcha_register']==1) { 
$recaptcha = recaptcha();
if ($recaptcha!=1)  $stop .= _CAPTCHAERROR."<br>";
}

$ip = getenv("REMOTE_ADDR");

if (!$name || !$login || !$email || !$pass) $stop .= _INSTALL21."<br>";


 if (!$stop) {
 $pass2=md5($pass);
 	list($islogin) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  admins WHERE login = '".$login."'"));
 	if ($islogin) $stop .= _ISLOGIN."<br>";
    list($ismail) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  admins WHERE email = '".$email."'"));
 	if ($ismail) $stop .= _ISMAIL."<br>";

 	$login_check = preg_match( '/^[a-z\d]{4,20}$/i', trim($login));
 	if ($login_check==0) $stop .= _LOGINWRONG."<br>";

 	$name_check = preg_match( '/^[a-zA-Zа-яёА-ЯЁ]+$/u', trim($name));
 	if ($name_check==0) $stop .= _NAMEWRONG."<br>";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stop .= _MAILWRONG."<br>";
}
$passlong = strlen($pass);
if ($passlong<6) {
	$stop .= _PASSWRONG."<br>";
}
 	 if (!$stop) {
 	  $result = $SxGeo->getCityFull($ip);

$timezone = $result['region']['timezone'];

$ip_arr = geoip_new($ip);

if ($settings['email_confirm']==1) {
   $email_check = 0; 
} else $email_check = 1;

 	 $db->sql_query('INSERT INTO admins (id, login, name, pass, email, ip, date, cc, telegram, skype, reg_from, email_check, city)
        VALUES (NULL, "'.$login.'", "'.$name.'", "'.$pass2.'", "'.$email.'",  "'.$ip.'", CURRENT_DATE(),  "'.$ip_arr['cc'].'",  "'.$telegram.'", "'.$skype.'", "'.$from.'", "'.$email_check.'", "'.$ip_arr['city'].'")');
        $admin_id = $db->sql_nextid();
     $db->sql_query('INSERT INTO settings (id, admin_id, name, value, created)
        VALUES (NULL, "'.$admin_id.'", "timezone", "'.$timezone.'", now())');
      $db->sql_query('INSERT INTO settings (id, admin_id, name, value, created)
        VALUES (NULL, "'.$admin_id.'", "lang", "'.$sellang.'", now())');   
        
         $db->sql_query('INSERT INTO balance (admin_id) VALUES ("'.$admin_id.'")');
        
        // если юзер зашел с реф кукой и у его рефовода включена реф программа
        if ($referal) {
            list($referan_on) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  admins WHERE id='".$referal."' AND ref_active = '1'"));
            if ($referan_on) {
        $db->sql_query('INSERT INTO referals (id, date, owner, admin_id)
        VALUES (NULL, now(), "'.$referal.'", "'.$admin_id.'")');    
        }  
        }
        $check_code = md5($admin_id.$email.$config['global_secret']);
        $title = _NEWUSER;
        $content = _NEWUSERTEXT;
        $content = str_replace("[login]", $login, $content);
        $content = str_replace("[pass]", $pass, $content);
        $content = str_replace("[url]", "https://".$settings['siteurl']."/admin/index.php", $content);
        if ($email_check==0) {
        $content .= "<br>----><br>"._CONFIRM_MAIL." - <a href=https://".$settings['siteurl']."/admin/index.php?m=login&check_mail=".$email."&check_code=".$check_code."&uid=".$admin_id.">https://".$settings['siteurl']."/admin/index.php?m=login&check_mail=".$email."&check_code=".$check_code."&uid=".$admin_id."</a>  <br>----><br>";    
        }

        newmail($admin_id, $email, $title, $content, $sellang);
        
        if ($email_check==0) {
        redir("index.php?m=login&email_check=1");    
        } else {
        redir("index.php?m=login&success=1");
        }
        exit;
 	 }
 }

} elseif($blocked_ip==1) {
  $stop = _IP_BLOCKED;  
}

if ($stop) {
status($stop, 'danger');
 }
 
$langsel[$lang] = 'selected';
$langs = array_flip(explode(',', $settings['langs']));
       ?>


	<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
        <div align=center><img src="images/logo-blue.png" width=200 border=0></div>
			<h3 class="text-center"><?php echo _REGISTER; ?></h3>
			<div class="login-form">
             <?php
                if ($settings['register_on']==1) {
                 ?>
                  <form action="index.php?m=register" method="post" id="checkvalid">
                  <div class="form-group form-floating-label">
					 <label><?php echo _LANG1; ?></label>
                            <select name="sellang" class="form-control-sm form-control col col-md-12">
                                    <?php
   foreach ($langs as $key => $value) {
   echo "<option value=\"".$key."\" ".$langsel[$key].">".$key."</option>";
   }
   ?> 
                                    </select>
				</div>
				<div class="form-group form-floating-label">
					<input  id="fullname" name="name" type="text" class="form-control input-border-bottom" required>
					<label for="fullname" class="placeholder"><?php echo _USERNAME; ?>*</label>
				</div>
                <div class="form-group form-floating-label">
					<input  id="login" name="login" type="text" class="form-control input-border-bottom" required>
					<label for="login" class="placeholder"><?php echo _LOGIN; ?>*</label>
				</div>
				<div class="form-group form-floating-label">
					<input  id="email" name="email" type="email" class="form-control input-border-bottom" required>
					<label for="email" class="placeholder">Email*</label>
				</div>
                <div class="form-group form-floating-label">
					<input  id="telegram" name="telegram" type="text" class="form-control input-border-bottom">
					<label for="telegram" class="placeholder">Telegram</label>
				</div>
                <div class="form-group form-floating-label">
					<input  id="skype" name="skype" type="text" class="form-control input-border-bottom">
					<label for="skype" class="placeholder">Skype</label>
				</div>
				<div class="form-group form-floating-label">
					<input  id="passwordsignin" placeholder="> 6 symb" name="pass" type="password" class="form-control input-border-bottom" required>
					<label for="passwordsignin" class="placeholder"><?php echo _PASS; ?></label>
					<div class="show-password">
						<i class="icon-eye"></i>
					</div>
				</div>
				<div class="row form-sub m-0">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="agree" id="agree" required>
						<label class="custom-control-label" for="agree"><a href="#" data-toggle="modal" data-target="#rules"><?php echo _AGREE_RULES; ?></a></label>
					</div>
                     <input type="hidden" name="token" id="token">
                        <input type="hidden" name="action" id="action">
                            <?php
                           if ($settings['captcha_register']==1) { 
                            ?>
                        <script src='https://www.google.com/recaptcha/api.js'></script>
<div class="g-recaptcha" data-sitekey=" <?php echo $settings['google_recaptcha_public']?>"></div>
                        <?php
                         }
                            ?>
				</div>
				<div class="form-action">
                    <button type="submit" class="btn btn-primary btn-rounded btn-login"><?php echo _REGISTER; ?></button>
                      <input name="save" type="hidden" value="1">
				</div>
                </form>
                 <?php
               } else {
    
             status(_REGISTER_OFF.": ".$settings['support_mail'], 'danger');
                 }  
                ?>  
			</div>
		</div>
	</div>
	<script src="assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/atlantis.min.js"></script>

          <div class="modal fade" id="rules" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _RULES ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=sites" method="post">

                               <?php
                              include("../views/rules_".$lang.".txt");
                              ?>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                            </div>
                            <input name="editsite" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
 <?php
 echo content_name('metriks', 'code');   
 ?>                </div>
</body>
</html>