<?php

error_reporting(E_WARNING);
ini_set('display_errors', true);

require_once("../include/func.php");
require_once("../admin/forms.php");
require_once('../include/SxGeo.php');

header('Content-Type: text/html; charset=utf-8');
$lang1 = text_filter($_REQUEST['lang1']);
$lang = text_filter($_REQUEST['lang']);
$SxGeo = new SxGeo('../include/SxGeoMax.dat');

if ($lang1) $lang=$lang1;

if (!$lang) {
$locale = get_lang();
if ($locale=='ru') {
$lang = 'ru';
} else $lang = 'en';
}

$lcheck[$lang] = "selected";
require_once("../admin/langs/$lang.php");

$page = intval($_REQUEST['page']);

function head() {
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
	<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
	<title>3xpush - "._INSTALLSYSTEM."</title>
    <meta name=\"description\" content=\"<?php echo _DESCRIPTION; ?>\">
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel=\"apple-touch-icon\" href=\"images/apple-icon.png\">
    <link rel=\"icon\" href=\"../favicon.png\" type=\"image/x-icon\"/>

	<!-- Fonts and icons -->
	<script src=\"../admin/assets/js/plugin/webfont/webfont.min.js\"></script>
	<script src=\"../admin/assets/js/core/jquery.3.2.1.min.js\"></script>
	<script>
		WebFont.load({
			google: {\"families\":[\"Lato:300,400,700,900\"]},
			custom: {\"families\":[\"Flaticon\", \"Font Awesome 5 Solid\", \"Font Awesome 5 Regular\", \"Font Awesome 5 Brands\", \"simple-line-icons\"], urls: ['../assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>
	
	<!-- CSS Files -->
	<link rel=\"stylesheet\" href=\"../admin/assets/css/bootstrap.min.css\">
	<link rel=\"stylesheet\" href=\"../admin/assets/css/atlantis.css\">
    <link rel=\"stylesheet\" href=\"../admin/css/main.css\">
</head>
<body class=\"login\">
<div class=\"wrapper wrapper-login\">
<div class=\"container container-login animated fadeIn\">
<div align=center><img src=\"../admin/images/logo-blue.png\" width=200 border=0></div>";
}

function foot() {
	echo "<script>
function goBack() {
  window.history.back();
}
</script>\n";

echo "</div>
	</div>
	<script src=\"../admin/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js\"></script>
	<script src=\"../admin/assets/js/core/popper.min.js\"></script>
	<script src=\"../admin/assets/js/core/bootstrap.min.js\"></script>
	<script src=\"../admin/assets/js/atlantis.min.js\"></script>
</body>
</html>
\n";
}


if (!$page) {
head();
 $num = $page + 1;
 echo "<div class=\"login-form\">
 <form action=\"index.php\" method=get>
                        <div class=\"form-group\">
                            <label>"._INSTALL1." (".$num." / 5)</label>
                            <select size=\"1\" name=\"lang1\" onchange=\"this.form.submit()\" class=\"form-control\">
                            <option value=\"ru\" ".$lcheck['ru'].">Русский</option>
                            <option value=\"en\" ".$lcheck['en'].">English</option>
                            </select>
                        </div>
                           <center> <button type=\"submit\" name=\"page\" value=\"1\" class=\"btn btn-primary btn-flat m-b-15\">"._INSTALL2."</button></center>
                            <input name=\"lang\" type=\"hidden\" value=\"$lang\">
  </form></div>\n";
foot();

}

if ($page==1) {
head();
$num = $page + 1;

if ($_SERVER['SERVER_NAME']=='localhost') $ssl=0; else $ssl=1;

$need_component = array('PHP' => '5.6', 'HTTPS' => $ssl);

$chek['PHP'] = phpversion();
$chek['HTTPS'] = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 1 : 0;

echo "<form action=\"index.php\"  method=post>
<h4>"._INSTALL3." (".$num." / 5)</h4>
<ul>";
foreach ($need_component as $key => $value) {
	    if ($chek[$key]>=$value) $status = "<b class=green>OK</b>"; else {$status = "<b class=red>NO</b>"; $disable = "disabled=\"disabled\""; }
echo "<li>".$key." (".$value.") - ".$status."</li>";
}
echo "</ul>";
 if ($disable) {
   status("<span class=\"badge badge-pill badge-danger\"><i class=\"fa fa-warning\"></i></span> "._INSTALL4, 'danger');
 } else  $next=1;

echo "<button type=\"submit\" name=\"page\" value=\"2\" class=\"btn btn-primary btn-flat m-b-15\" ".$disable.">"._INSTALL2."</button>
<input name=\"next\" type=\"hidden\" value=\"$next\">
<input name=\"lang\" type=\"hidden\" value=\"$lang\">
</form>";

foot();
}


if ($page==2) {
head();
if (!$_POST['next']) exit;
$num = $page + 1;
$skey=gen_pass(19);
$ip = getenv("REMOTE_ADDR");
$result = $SxGeo->getCityFull($ip);
$timezone = $result['region']['timezone'];    
                    function tz_list() {
  $zones_array = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
    $zones_array[$key]['zone'] = $zone;
    $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    $zones_array[$key]['mysql_diff'] =  date('P', $timestamp);
  }
  return $zones_array;
}

if ($_SERVER['SERVER_NAME']=='localhost') $check = "checked";
echo "<form action=\"index.php\" method=post name=install id=\"install\" onsubmit=\"return validate_form ( );\">
<h4>"._INSTALL5." (".$num." / 5)</h4>";
echo "<table class=\"table-small\">
<tbody>
<tr><td width=50%><b>"._GSECRET."</b></td><td><input name=\"global_secret\" id=global_secret type=\"text\" value=\"".$skey."\"></td></tr>
<tr><td><b>"._INSTALL6."</b></td><td><input name=\"dbhost\" id=dbhost type=\"text\" value=\"localhost\"></td></tr>
<tr><td><b>"._INSTALL7."</b></td><td><input name=\"dbuname\" id=dbuname type=\"text\" value=\"\"></td></tr>
<tr><td>"._INSTALL8."</td><td><input name=\"dbpass\" id=dbpass type=\"text\" value=\"\"></td></tr>
<tr><td><b>"._INSTALL9."</b></td><td><input name=\"dbname\" id=dbname type=\"text\" value=\"push\"></td></tr>
<tr><td>"._INSTALL14."</td><td><input name=\"memcache_master_ip\" type=\"text\" value=\"\" placeholder=\"ip or localhost\"> <input name=\"memcache_master_port\" type=\"text\" value=\"\" placeholder=\"port\"></td></tr>
<tr><td>"._INSTALL15."</td><td><input name=\"root\" type=\"text\" value=\"/\"></td></tr>
<tr><td>"._OPTIONS31."</td><td>
<select name=\"timezone\" style=\"width: 200px;\">
                                    <option value=\"\"></option>";
                                    
                                        foreach(tz_list() as $t) {
                                         if ($timezone==$t['zone']) $sel = 'selected';  else $sel='';

                                        echo "<option value=\"".$t['zone']."_".$t['mysql_diff']."\" ".$sel.">
                                        ".$t['diff_from_GMT'] . " - " . $t['zone']."
                                         </option>";
                                         }
                                      
                                    echo "</select>
                                    </td></tr>
<tr><td>localhost</td><td><input type=\"checkbox\" value=\"1\" name=\"local\" ".$check." /></td></tr>
</tbody>
</table>\n";

echo "<button type=\"submit\" name=\"page\" value=\"3\" class=\"btn btn-primary btn-flat m-b-15\">"._INSTALL2."</button>
<input name=\"next\" type=\"hidden\" value=\"1\">
<input name=\"lang\" type=\"hidden\" value=\"$lang\">
</form>";

echo "<script>

<!--

function validate_form ( )
{
	valid = true;

        if ( document.install.global_secret.value == \"\" || document.install.dbhost.value == \"\" || document.install.dbuname.value == \"\" || document.install.dbname.value == \"\" )
        {
                alert ( \""._FEEDSTEXT1.".\" );
                valid = false;
        }

        return valid;
}

//-->
</script>";

foot();
}

if ($page==3) {
head();

if (!$_POST['next']) exit;
$num = $page + 1;
$is_local = intval($_POST['local']);
$dbhost = text_filter($_POST['dbhost']);
$dbuname = text_filter($_POST['dbuname']);
$dbpass = text_filter($_POST['dbpass']);
$dbname= text_filter($_POST['dbname']);
$global_secret= text_filter($_POST['global_secret']);
$memcache_master_ip= text_filter($_POST['memcache_master_ip']);
$memcache_master_port= text_filter($_POST['memcache_master_port']);
$timezone= text_filter($_POST['timezone'], 2);
$root= text_filter($_POST['root']);
if (!$root) $root = '/';

if ($timezone) {
      $values = explode("_", $timezone);

      $timezone = $values[0];
      $zone_diff = $values[1];
} else {
    $timezone = "Europe/Moscow";
}

if ($memcache_master_ip) {
$memcached = new Memcache;
if (!$memcached->pconnect($memcache_master_ip, $memcache_master_port)) $stop = "<br>Memcache connect error!";  
}
echo "<form action=\"index.php\" method=post name=install id=\"install\">
<h4>"._INSTALL5." (".$num." / 5)</h4>";

if ($dbhost && $dbuname && $dbname && $global_secret && !$stop) {

// Create connection
$conn = new mysqli($dbhost, $dbuname, $dbpass, $dbname);
// Check connection
if ($conn->connect_error) {
   status(_INSTALL11.": " . $conn->connect_error, 'danger');
} else {
  status("Mysql - "._INSTALL13, 'success');



$conf = fopen("../include/config.php", 'w');
$str = '<?php
$root = $_SERVER[\'DOCUMENT_ROOT\']."'.$root.'";
$config[\'global_secret\'] = "'.$global_secret.'";
$config[\'proj_timezone\'] = "'.$timezone.'";
$config[\'local_proj\'] = '.$is_local.';
$config[\'master_host\'] = "'.$dbhost.'";
$config[\'master_user\'] = "'.$dbuname.'";
$config[\'master_pass\'] = "'.$dbpass.'";
$config[\'master_db\'] = "'.$dbname.'";
$config[\'memcache_ip\'] = "'.$memcache_master_ip.'";
$config[\'memcache_port\'] = "'.$memcache_master_port.'";

?>';
fwrite($conf, $str);
fclose($conf);

echo "<div id=load><img src=load.gif valign=absmiddle> "._INSTALL17."...</div><br /><br />";

echo "<script>
$.get('db.php', {go: 1}).done(function(data) {
   if(data==\"ok\"){
        document.getElementById('load').innerHTML = '<div class=\"alert alert-success\" role=\"alert\">"._INSTALL18."</div>';
        document.getElementById('nextpage').disabled = false;
   }
   if (~data.indexOf(\"FAIL\")){
        document.getElementById('load').innerHTML = '<div class=\"alert alert-danger\" role=\"alert\">'+data+'</div>';
   }
});
</script>";


}

} else {
status(_INSTALL11.$stop, 'danger');
}

echo "<button type=\"submit\" name=\"page\" value=\"2\" class=\"btn btn-warning btn-flat m-b-15\">"._INSTALL12."</button> &nbsp;&nbsp;

<button type=\"submit\" name=\"page\" id=nextpage value=\"4\" class=\"btn btn-primary btn-flat m-b-15\" disabled=\"disabled\">"._INSTALL2."</button>
<input name=\"next\" type=\"hidden\" value=\"1\">
<input name=\"lang\" type=\"hidden\" value=\"$lang\">
</form>";

foot();
}

if ($page==4) {
head();
if (!$_POST['next']) exit;
$num = $page + 1;
echo "<form action=\"index.php\" method=post name=install id=\"install\" onsubmit=\"return validate_form ( );\">
<h4>"._INSTALL16." ($num / 5)</h4>";
echo "<table class=\"table-small\">
<tbody>
<tr><td>"._INSTALL7."</td><td><input name=\"login\" id=login type=\"text\" value=\"admin\" ></td></tr>
<tr><td>"._INSTALL8."</td><td><input name=\"pass\" id=pass type=\"text\" value=\"\"></td></tr>
<tr><td>Email</td><td><input name=\"email\" id=email type=\"text\" value=\"\" ></td></tr>
<tr><td>"._INSTALL20."</td><td><input name=\"options[domain]\" id=domain type=\"text\" value=\"".$_SERVER['HTTP_HOST']."\" ></td></tr>
<tr><td>Firebase Server key</td><td><input name=\"options[server_key]\" id=server_key type=\"text\" value=\"\" ></td></tr>
<tr><td>Firebase config</td><td><textarea name=\"options[firebase_conf]\" id=firebase_conf rows=5 cols=30 wrap=\"off\" ></textarea></td></tr>
</tbody>
</table>\n";

echo "<button type=\"submit\" name=\"page\" id=nextpage value=\"5\" class=\"btn btn-primary btn-flat m-b-15\">"._INSTALL2."</button>
<input name=\"next\" type=\"hidden\" value=\"1\">
<input name=\"lang\" type=\"hidden\" value=\"$lang\">
</form>";

echo "<script>

<!--

function validate_form ( )
{
	valid = true;

        if (document.install.email.value == \"\" ||  document.install.login.value == \"\" || document.install.pass.value == \"\" || document.install.domain.value == \"\" || document.install.server_key.value == \"\" || document.install.firebase_conf.value == \"\" || document.install.mass_mess_count.value == \"\" || document.install.send_every.value == \"\"  )
        {
                alert ( \""._INSTALL21.".\" );
                valid = false;
        }

        return valid;
}

//-->
</script>";

foot();
}

if ($page==5) {
require_once("../include/mysql.php");
head();
if (!$_POST['next']) exit;
$num = $page + 1;
$login = text_filter($_POST['login']);
$pass = text_filter($_POST['pass']);
$email = text_filter($_POST['email']);
if (!$login || !$pass || !$email) {
status(_INSTALL21, 'danger');
exit;
}

foreach ($_POST['options'] as $key => $value) {
      if ($key=='firebase_conf') {
    $value = str_replace("firebaseConfig", "config", $value);       
     }
  $db->sql_query("UPDATE settings SET value='$value' WHERE name='$key'");
    }

$db->sql_query("INSERT INTO admins (login, pass, role, root, email)
VALUES ('".$login."', '".md5($pass)."', '1', '1', '".$email."')") or $error = mysql_error();

$files = array("closing-content.js", "firebase-messaging-sw.js", "new.js", "up/popup-script.js", "service-worker.js");

foreach ($files as $file) {
	$content = file_get_contents($file);
	if ($content) {
	$content = str_replace("FIREBASE_CONF", $_POST['options']['firebase_conf'], $content);
	$content = str_replace("SITE_URL", $_POST['options']['domain'], $content);
	if (file_put_contents("../".$file, $content)==false) {
	$error .= "$file - "._INSTALL24;
	}
	} else {
	$error .= "$file - "._INSTALL23;
	}
}
if ($error) {
status($error, 'danger');
} else {
status(_INSTALL22, 'success');
 }
echo "<form action=\"https://".$_POST['options']['domain']."/admin/index.php\" method=post>
<button type=\"submit\" name=\"page\" id=nextpage value=\"5\" class=\"btn btn-primary btn-flat m-b-15\">"._ENTER."</button>
<input name=\"m\" type=\"hidden\" value=\"login\">
<input name=\"login\" type=\"hidden\" value=\"$login\">
</form>";

foot();
}