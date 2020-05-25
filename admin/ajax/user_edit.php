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
$user_id = intval($params[0]);
$check_code = text_filter($params[1]);

if (!$user_id || !$check_code) {echo 'params error';exit;}

$code = md5($config['global_secret'].$user_id);
if ($check_code!=$code) {echo 'check code error';exit;}

$admins = admins_balance("AND a.id=$user_id");

if ($admins[$user_id]['email_check']==1) $check = 'checked';
if ($admins[$user_id]['ref_active']==1) $ref_active = 'checked';
if ($admins[$user_id]['deny_ads']==1) $deny_ads = 'checked';
if ($admins[$user_id]['deny_sending']==1) $deny_sending = 'checked';
if ($admins[$user_id]['check_city']==1) $check_city = 'checked';
if ($admins[$user_id]['root']==1) $checked['root'] = 'checked';
if ($admins[$user_id]['is_support']==1) $checked['is_support'] = 'checked';
if ($admins[$user_id]['good_user']==1) $checked['good_user'] = 'checked';
                            
echo "<table><tbody>";
echo "<tr><td>"._USERNAME."</td><td><input name=\"name\" type=\"text\" class=\"longinput\" value=\"".$admins[$user_id]['name']."\"></td> </tr>";
echo "<tr><td>Email</td><td><input name=\"email\" class=\"longinput\" type=\"text\" value=\"".$admins[$user_id]['email']."\"></td></tr>";
echo "<tr><td>Telegram</td><td><input name=\"telegram\" class=\"longinput\" type=\"text\" value=\"".$admins[$user_id]['telegram']."\"></td></tr>";
echo "<tr><td>Skype</td><td><input name=\"skype\" class=\"longinput\" type=\"text\" value=\"".$admins[$user_id]['skype']."\"></td></tr>";
echo "<tr><td>Email "._ISCHECKED."</td><td> <input type=\"checkbox\" name=\"email_check\" value=\"1\" ".$check." /></td></tr>";
echo "<tr><td>"._REF_PROGRAM."</td><td> <input type=\"checkbox\" name=\"ref_active\" value=\"1\" ".$ref_active." /></td></tr>";
echo "<tr><td>"._CLOSE_SENDING."</td><td> <input type=\"checkbox\" name=\"deny_sending\" value=\"1\" ".$deny_sending." /></td></tr>";
echo "<tr><td>"._CITY_CHECK."</td><td> <input type=\"checkbox\" name=\"check_city\" value=\"1\" ".$check_city." /></td></tr>";
echo "<tr><td>"._GOOD_USER."</td><td> <input type=\"checkbox\" name=\"good_user\" value=\"1\" ".$checked['good_user']." /></td></tr>";
echo "<tr><td>"._NEWPASS."</td><td><input name=\"newpass\" class=\"longinput\" type=\"text\" value=\"\"></td></tr>";
echo "<tr><td colspan=2><strong>"._ADMIN_FUNC."</strong></td></tr>";
echo "<tr><td>SUPER ADMIN</td><td> <input type=\"checkbox\" name=\"root\" value=\"1\" ".$checked['root']." /> ".tooltip(_SUPER_ADM_TOOLTIP, 'rught')."</td></tr>";
echo "<tr><td>Support</td><td> <input type=\"checkbox\" name=\"is_support\" value=\"1\" ".$checked['is_support']." /> ".tooltip(_IS_SUPPORT, 'rught')."</td></tr>";
echo "</tbody>
</table><input name=\"edit\" type=\"hidden\" value=\"".$user_id."\">";