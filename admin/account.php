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

?>
<link rel="stylesheet" href="vendors/chosen/chosen.min.css">
        
<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _ACCOUNTOPTIONS ?></h4>
</div>
                            
         <?php

$score_save = intval($_POST['score_save']);
$save = intval($_POST['save']);
$accountsave = intval($_POST['accountsave']);
$name = text_filter($_POST['name']);
$login = text_filter($_POST['login']);
$email = text_filter($_POST['email']);
$newpass = text_filter($_POST['newpass']);
$newpass2 = text_filter($_POST['newpass2']);
$adddomain = intval($_POST['adddomain']);
$telegram = text_filter($_POST['telegram']);
$domain = text_filter($_POST['domain']);
$get_mail = intval($_POST['get_mail']);
$promo_mail = intval($_POST['promo_mail']);
$auto_money = intval($_POST['auto_money']);
$score_id = intval($_POST['score_id']);
$notif_push = intval($_POST['notif_push']);
$check_city = intval($_POST['check_city']);
$skype = text_filter($_POST['skype']);
$save_lang = text_filter($_POST['lang']);
$deletedomain = intval($_POST['deletedomain']);
$langs = array_flip(explode(',', $settings['langs']));
$edit = intval($_POST['edit']);
$delete = intval($_POST['delete']);

$ip = getenv("REMOTE_ADDR");
$agent = getenv("HTTP_USER_AGENT");

$mail_code = text_filter($_GET['code']);
$newmail = text_filter($_GET['newmail']);
$sendmail = intval($_GET['sendmail']);

// mail confirmation
if ($newmail && $mail_code) {
  $check_code = md5($check_login['email'].$config['global_secret']);
if ($check_code!=$mail_code) {
  status('error code', 'danger');
} else {
   $db->sql_query('UPDATE admins SET email_check=1 WHERE id='.$check_login['id'].'');
   jset($check_login['id'], _MAILAPPROVER.": ".$check_login['email'].""); 
  status(_MAILAPPROVER, 'success'); 
} 
}

$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;


// delete domain
if ($deletedomain && $check_login['role']==1) {
    $deldomain = get_onerow('domain', 'domains', "id=".$deletedomain." AND admin_id=".$check_login['getid']."");
    $db->sql_query('DELETE FROM domains WHERE id='.$deletedomain.' AND admin_id='.$check_login['getid'].'');// or $stop = mysqli_error();
    jset($check_login['id'], _DOMAINDELETED); 
if ($check_login['id']!=$check_login['getid']) {
alert(_DOMAINDELETED.": $deldomain (user: ".$check_login['login'].")", $check_login['getid']); 
}
    status(_DOMAINDELETED, 'success');
}
// add domain
if ($adddomain==1 && $domain && $check_login['role']==1) {
    if (is_valid_domain_name($domain)!=1) $stop = _DOMAINFAIL;
    
    if (!$stop) {

    $db->sql_query('INSERT INTO domains (id, admin_id, domain, updated)
VALUES (NULL, '.$check_login['getid'].', "'.$domain.'", now())');// or $stop = mysqli_error();
jset($check_login['id'], _DOMAINADDED.": $domain");
if ($check_login['id']!=$check_login['getid']) {
alert(_DOMAINADDED.": $domain (user: ".$check_login['login'].")", $check_login['getid']); 
}
status(_DOMAINADDED, 'success');
} else {
status($stop, 'danger');    
}
}

// saving wallets
if ($score_save==1 && $check_login['role']==1) {
    $scores = $_POST['scores'];
    if (is_array($scores)) {

     $is_out = get_onerow('COUNT(id)', 'payment', "admin_id=" . $check_login['getid'] . " AND type=3 AND status=0"); // првоеряем нет ли у юзеру ожидающих выплат, если есть, то запрещаем менять кошельки
       if ($is_out) $stop = _CANNOT_CHANGE_SCORE;

        if (!$stop) {
            $payments_type = payments_type();
            
            $last_data = admins("AND id=".$check_login['getid']."");
            
            $db->sql_query('UPDATE admins SET auto_money="'.$auto_money.'", score_id="'.$score_id.'" WHERE id='.$check_login['getid'].'');
            
            if ($auto_money==1 && $last_data[$check_login['id']]['auto_money']==0) {
                $alert = ". "._AUTO_MONEY_ON;
            } elseif ($auto_money==0 && $last_data[$check_login['id']]['auto_money']==1) {
                $alert = ". "._AUTO_MONEY_OFF;
            }
            if ($score_id!=$last_data[$check_login['id']]['score_id']) {
                $alert .= ". "._AUTO_MONEY_CHANGE;
            } 
            
         foreach ($scores as $id => $score) {
            $score = text_filter($score);
            $id = intval($id);
            $check = md5($score.$config['global_secret']);
            $db->sql_query("INSERT INTO admins_score (id, admin_id, payment_id, score, check_code)
                  VALUES (NULL, '" . $check_login['getid'] . "', '".$id."', '".$score."', '".$check."')
                  ON DUPLICATE KEY UPDATE
                  score = '".$score."', check_code = '".$check."'");
                  $scores_arr[$payments_type[$id]['title']] = $score;
         }
         foreach ($scores_arr as $key => $value) {
         $scores2 .= "$key: $value, ";
         }
         $scores2 = rtrim($scores2, ",");
         jset($check_login['id'], _SCOREUPDATE.": $scores2".$alert); 
         if ($check_login['id']!=$check_login['getid']) {
         alert(_SCOREUPDATE.": $scores2 (user: ".$check_login['login'].")".$alert, $check_login['getid']); 
         }
         status(_SCOREUPDATE, 'success');
         $title = $settings['sitename']." - "._SCOREUPDATE;
         $content = _SCOREUPDATEEMAIL.": ".$scores2."<br>".$alert;

         newmail($check_login['getid'], $check_login['email'], $title, $content, $lang);
    } else {
        status($stop, 'danger');
    }

    }
}


// saving account settings
if ($accountsave==1) {
  if ($newpass && $newpass2) {
	if ($newpass!=$newpass2) {
	$stop=_NEWPASSWRONG;
	} else {
	$newpass=", pass='".md5($newpass)."'";
	$logout=1;
    $mail_info .= _PASSCHANGE;
    $mail_info .= "<br><br>
    IP: ".$ip.", UA: ".$agent." 
    <hr>";
	}
} else $newpass='';

if ($email && $email!=$check_login['email']) {
     $check_code = md5($email.$config['global_secret']);
    $mail_info .= _NEWMAIL;
    $mail_info .= "<br><br><a href=https://".$settings['siteurl']."/admin/index.php?m=account&code=".$check_code."&newmail=".$email."&tab=2>https://".$settings['siteurl']."/admin/index.php?m=account&code=".$check_code."&newmail=".$email."&tab=2</a>";
    $check_mail = ", email_check=0, check_code='".$check_code."'";
    }

 if (!$email) $stop .= _MAIL_EMPTY."<br>";
 if (!$login) {$stop .= _LOGIN_EMPTY."<br>";} else {
    $is_login = get_onerow('id', 'admins', "id!=".$check_login['getid']." AND login='".$login."'");
    if ($is_login) $stop .= _ISLOGIN."<br>";
 }
 
 

  if(!$stop) {
  $db->sql_query("UPDATE admins SET skype='$skype', check_city='$check_city', notif_push='$notif_push',  get_mail='$get_mail', promo_mail='$promo_mail', telegram='$telegram', name='".$name."', login='".$login."', email='".$email."' ".$newpass." ".$check_mail." WHERE id=".$check_login['getid']."");
  
  foreach ($_POST['options'] as $key => $value) {
$value = text_filter($value);
    if ($key=='timezone') {
      $values = explode("__", $value);
      $value = $values[0];
      $zone_diff = $values[1];
    }  
    if ($key=='lang') {
     if (!in_array($value, $langs)) $stop .= "lang error<br>";   
        }
        if (!$stop) {
   $db->sql_query("INSERT INTO settings (id, admin_id, name, value, created)
                      VALUES (NULL, ".$check_login['getid'].", '".$key."', '".$value."', now())
                       ON DUPLICATE KEY UPDATE
                      value = '".$value."'") or die (mysqli_error());
                      
    } else {
    status($stop, 'danger');    
    }
    }
    
  $check_login = check_login();
  jset($check_login['id'], _ACCOUNTUPDATE); 
  if ($check_login['id']!=$check_login['getid']) {
alert(_ACCOUNTUPDATE." (user: ".$check_login['login'].")", $check_login['getid']); 

}    
status(_ACCOUNTUPDATE, 'success');
redir("?m=account&tab=1");
  } else {

    status($stop, 'danger');
  }

}
if ($save==1 && $check_login['role']==1) {

$nochecked=array('cpc_send'=>'cpc_send','cpv_send'=>'cpv_send','enable_messaging'=>'enable_messaging','block_ctr'=>'block_ctr','block_unsubs'=>'block_unsubs','image_send'=>'image_send','check_url'=>'check_url','usepostback'=>'usepostback');

foreach ($_POST['options'] as $key => $value) {

    if (in_array($key, $nochecked)) unset($nochecked[$key]);

    if ($key=='timezone') {
      $values = explode("__", $value);
      $value = $values[0];
      $zone_diff = $values[1];
    }
    if ($key=='send_every') {
         if ($value < 1) $stop = _OPTIONS2." - "._MINIMAL_VAL.": 1<br>";
        }
     if ($key=='send_fornew') {
         if ($value < 1) $stop .= _OPTIONS_AFTERCREATE." - "._MINIMAL_VAL.": 1<br>";
        }
     if ($key=='send_afterview') {
         if ($value < 1) $stop .= _OPTIONS_AFTERVIEW." - "._MINIMAL_VAL.": 1<br>";
     } 
     if ($key=='send_afterclick') {
         if ($value < 1) $stop .= _OPTIONS_AFTERCLICK." - "._MINIMAL_VAL.": 1<br>";
     }           
     if (!$stop) {
   $db->sql_query("INSERT INTO settings (id, admin_id, name, value, created)
                      VALUES (NULL, ".$check_login['getid'].", '".$key."', '".$value."', now())
                       ON DUPLICATE KEY UPDATE
                      value = '".$value."'") or die (mysqli_error());
     }

    }

    if (!empty($nochecked)) {
    foreach ($nochecked as $key => $value) {
     $db->sql_query("INSERT INTO settings (id, admin_id, name, value, created)
                      VALUES (NULL, ".$check_login['getid'].", '".$value."', 0, now())
                       ON DUPLICATE KEY UPDATE
                      value = 0");
    }
    }
    jset($check_login['id'], _OPTIONS23); 
      if ($check_login['id']!=$check_login['getid']) {
alert(_OPTIONS23." (user: ".$check_login['login'].")", $check_login['getid']); 
}  

    if ($stop) {
    status($stop, 'danger');
    } 
    status(_OPTIONS23, 'success');
    
    if ($logout==1) { 
    redir("?logout=1");
    }
    }
    
  if ($sendmail==1) {
     $check_code = md5($check_login['email'].$config['global_secret']);
    $mail_info .= _NEWMAIL;
    $mail_info .= "<br><br><a href=https://".$settings['siteurl']."/admin/index.php?m=account&code=".$check_code."&newmail=".$check_login['email']."&tab=2>https://".$settings['siteurl']."/admin/index.php?m=account&code=".$check_code."&newmail=".$check_login['email']."&tab=2</a>";
    }
      
    if ($mail_info) {
     $title = $settings['sitename']." - "._ACCOUNTUPDATEMAIL;
    $mail = newmail($check_login['id'], $check_login['email'], $title, $mail_info, $lang);
    status(_MAILCHECK, 'success');
    }

$sql = "SELECT * FROM settings WHERE admin_id=0";
$settings1 = $db->sql_query($sql);
$settings1 = $db->sql_fetchrowset($settings1);
   foreach ($settings1 as $key1 => $val) {
   $settings[$val['name']] = $val['value'];
   }

$sql = "SELECT * FROM settings WHERE admin_id=".$check_login['getid']."";
$settings1 = $db->sql_query($sql);
$settings1 = $db->sql_fetchrowset($settings1);
if (is_array($settings1)) {
   foreach ($settings1 as $key1 => $val) {
   $settings[$val['name']] = $val['value'];
   }
  }

if ($settings['enable_messaging']==1) $enable_messaging1 = "checked";
if ($settings['cpv_send']==1) $cpv_send1 = "checked";
if ($settings['cpc_send']==1) $cpc_send1 = "checked";
if ($settings['dont_send_after_click']==1) $dont_send_after_click1 = "checked";
if ($settings['test']==1) $pushtest = "checked";
if ($settings['block_unsubs']==1) $block_unsubs1 = "checked";
if ($settings['image_send']==1) $image_send1 = "checked";
if ($settings['check_url']==1) $check_url1 = "checked";

if ($settings['sorttype']) {
$sortsel[$settings['sorttype']] = "checked";
} else $sortsel[1] = "checked";
if ($settings['myadssort']) {
$myadssortsel[$settings['myadssort']] = "checked";
} else $myadssortsel[1] = "checked";

$payments_type = payments_type("AND status=1 AND withdrowal=1");
$admins_score = admins_score("AND admin_id=".$check_login['getid']."");
$domains= domains("AND admin_id='".$check_login['getid']."'");

if ($check_login['email_check']!=1) {
    $email_check = "<a href=?m=account&tab=1&sendmail=1>"._MAILAPPROVE."</a>";
}
?>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
             <div class="card-body">
                 <?php
                  if ($check_login['role']==1) {
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
$ch=array();


$admin_info = admins("AND id=".$check_login['getid']."");
$admin_info = $admin_info[$check_login['getid']];
if ($admin_info['get_mail']==1) $ch[0] = 'checked';
if ($admin_info['promo_mail']==1) $ch[1] = 'checked';
if ($admin_info['auto_money']==1) $ch[2] = 'checked';
if ($admin_info['notif_push']==1) $ch[4] = 'checked';
if ($admin_info['check_city']==1) $ch[5] = 'checked';

                  ?>

<div class="default-tab">
<nav>
<div class="nav nav-tabs" id="nav-tab-1" role="tablist">


<a class="nav-item nav-link <?= $tab==1 ? 'active' : '' ?>" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-1" aria-selected="false" name="1"><?php echo _ACCOUNT; ?></a>
<?php if($settings['allow_options']==1) { ?>
<a class="nav-item nav-link <?= $tab==2 ? 'active' : '' ?>" id="nav-2-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-2" aria-selected="true" name="2"><?php echo _SENDOPTIONS; ?></a>
<?php } ?>
<a class="nav-item nav-link <?= $tab==4 ? 'active' : '' ?>" id="nav-4-tab" data-toggle="tab" href="#nav-4" role="tab" aria-controls="nav-4" aria-selected="false" name="4"><?php echo _FINANCE; ?></a>
<?php if($settings['allow_domains']==1) { ?>
<a class="nav-item nav-link <?= $tab==5 ? 'active' : '' ?>" id="nav-5-tab" data-toggle="tab" href="#nav-5" role="tab" aria-controls="nav-5" aria-selected="false" name="5"><?php echo _DOMAINS; ?></a>
<?php } ?>
</div>
</nav>
<div class="tab-content pl-3 pt-2" id="nav-tabContent-1">
<div class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>"" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">
     <?php if($settings['allow_options']==1) { ?>
        <form name="addsite" action="?m=account&tab=2" method="post">
<table class=table1>
<tbody>
<tr><td width=30%><?php echo _OPTIONS1; ?></td><td><input name="options[enable_messaging]" type="checkbox" value="1" <?php echo $enable_messaging1; ?>></td></tr>
<tr ><td><?php echo _OPTIONS2; ?></td><td><input name="options[send_every]" type="text" value="<?php echo $settings['send_every'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_EVERY_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERCREATE; ?></td><td><input name="options[send_fornew]" type="text" value="<?php echo $settings['send_fornew'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_FORNEW_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERVIEW; ?></td><td><input name="options[send_afterview]" type="text" value="<?php echo $settings['send_afterview'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_AFTERVIEW_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERCLICK; ?></td><td><input name="options[send_afterclick]" type="text" value="<?php echo $settings['send_afterclick'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_AFTERCLICK_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS3; ?></td><td><input name="options[user_messages]" type="text" value="<?php echo $settings['user_messages'] ?>"> <?php echo _OPTIONS18; ?> <input name="options[user_messages_days]" type="text" value=<?php echo $settings['user_messages_days'] ?>> дня  <?php echo tooltip(_USER_MESAGES_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS4; ?></td><td><input name="options[max_send]" type="text" value="<?php echo $settings['max_send'] ?>"> <?php echo _OPTIONS19; ?> <?php echo tooltip(_MAXSEND_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS5; ?></td><td><input name="options[max_adv_send]" type="text" value="<?php echo $settings['max_adv_send'] ?>"> <?php echo _OPTIONS19; ?> <?php echo tooltip(_MAXSEND_ADV_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS14; ?></td><td><textarea name="options[stopwords]" rows=5 cols=50 wrap="on"><?php echo $settings['stopwords']; ?></textarea>  <?php echo tooltip(_STOPWORDS_TOOLTIP); ?></td></tr>
                                    </tbody>
                             </table>
               <input name="save" type="hidden" value="1">
                      <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                             </form>
  <?php } ?>                           
</div>
<div class="tab-pane fade show <?= $tab==1 ? 'active' : '' ?>"" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab">
        <form name="addsite" action="?m=account&tab=1" method="post">
<table class=table1  width=100%>
<tbody>
<tr><td width=30%><?php echo _LANG1; ?></td><td>
    <select size="1" name="options[lang]">
  <?php
   foreach ($langs as $key => $value) {
    if ($lang==$key) $sel = 'selected'; else $sel = '';
   echo "<option value=\"$key\" $sel>$key</option>";
   }
   ?>   
    </select> 
</td></tr>
<tr><td><?php echo _USERNAME; ?></td><td><input name="name" type="text" value="<?php echo $admin_info['name'] ?>"></td></tr>
<tr><td><?php echo _INSTALL7; ?></td><td><input name="login" type="text" value="<?php echo $admin_info['login'] ?>"></td></tr>
<tr><td>E-mail</td><td><input name="email" type="text" value="<?php echo $admin_info['email'] ?>"> <?php echo $email_check; ?></td></tr>
<tr><td>Telegram</td><td><input name="telegram" type="text" value="<?php echo $admin_info['telegram'] ?>"></td></tr>
<tr><td>Skype</td><td><input name="skype" type="text" value="<?php echo $admin_info['skype'] ?>"></td></tr>
<tr><td><?php echo _MAILSTATUS; ?></td><td><input type="checkbox" name="get_mail" value="1" <?php echo $ch[0];  ?> /></td></tr>
<tr><td><?php echo _MAILSTATUS2; ?></td><td><input type="checkbox" name="promo_mail" value="1"  <?php echo $ch[1];  ?> /></td></tr>
<tr><td><?php echo _GET_PUSH_NOTIF; ?></td><td><input type="checkbox" name="notif_push" value="1"  <?php echo $ch[4];  ?> /> <?php echo tooltip(_GET_PUSH_NOTIF_TOOLTIP);  ?></td></tr>
<tr><td><?php echo _APPROVE_NEW_PLACE; ?></td><td><input type="checkbox" name="check_city" value="1"  <?php echo $ch[5];  ?> /> <?php echo tooltip(_APPROVE_NEW_PLACE_TOOLTIP);  ?></td></tr>
<tr><td><?php echo _NEWPASS; ?></td><td><input name="newpass" type="password" value=""></td></tr>
<tr><td><?php echo _REPEAT; ?></td><td><input name="newpass2" type="password" value=""></td></tr>
<tr ><td colspan="2"><b><?php echo _STAT; ?></b></td></tr>
<tr><td><?php echo _OPTIONS30; ?></td><td><input name="options[days_stat]" type="text" value="<?php echo $settings['days_stat'] ?>"> <?php echo tooltip(_STATPERIOD_TOOLTIP); ?></td></tr>
<tr><td><?php echo _OPTIONS31; ?></td><td><select data-placeholder="<?php echo _CHOSEREGION; ?>" name="options[timezone]" class="standardSelect" tabindex="1">
                                    <option value=""></option>
                                        <?php foreach(tz_list() as $t) {
                                         if ($settings['timezone']==$t['zone']) $sel = 'selected';  else $sel='';

                                        echo "<option value=\"".$t['zone']."__".$t['mysql_diff']."\" ".$sel.">
                                        ".$t['diff_from_GMT'] . " - " . $t['zone']."
                                         </option>";
                                         }
                                        ?>
                                    </select> <?php echo tooltip(_LOCALTIME_TOOLTIP); ?>
                                 </td></tr>
</table>
  <input name="accountsave" type="hidden" value="1">
                      <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                             </form>
</div>
<div class="tab-pane fade show <?= $tab==4 ? 'active' : '' ?>"" id="nav-4" role="tabpanel" aria-labelledby="nav-4-tab">
        <form name="score" action="?m=account&tab=4" method="post">
<table class=table1  width=100%>
<tbody>
<?php
$score=array();
if (is_array($payments_type)) {
foreach ($payments_type as $key => $value) {
 if (is_array($admins_score)) {
    foreach ($admins_score as $key2 => $value2) {
        if ($value2['payment_id']==$key) $score[$key] = $value2['score'];
    }
 }
echo "<tr><td width=30%>".$value['title']."</td><td><input name=\"scores[".$key."]\" type=\"text\" value=\"".$score[$key]."\"></td></tr>";
}

?>
<tr><td><?php echo _AUTO_MONEY; ?></td><td><input type="checkbox" name="auto_money" value="1" <?php echo $ch[2];  ?> /> <?php echo tooltip(_AUTO_MONEY_INFO, 'right') ?> </td></tr>

<?php


 if (is_array($admins_score)) {
echo "<tr><td>"._AUTO_MONEY_SCORE."</td><td> <select size=\"1\" name=\"score_id\">";
foreach ($admins_score as $key => $value) {
    $name = $payments_type[$value['payment_id']]['title'];
    if (!$name)  continue;
    if ($admin_info['score_id']==$key) $sel = 'selected'; else $sel ='';
	echo "<option value=\"".$key."\" ".$sel.">".$name."</option>";
    }
echo "</select></td></tr>";
}
}
$settings['ns_domain'] = str_replace(",", "<br>", $settings['ns_domain']);
?>
</table>
<input name="score_save" type="hidden" value="1">
<button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                             </form>
</div>
<div class="tab-pane fade show <?= $tab==5 ? 'active' : '' ?>"" id="nav-5" role="tabpanel" aria-labelledby="nav-5-tab">
<?php if($settings['allow_domains']==1) { ?>
                          <div class="addbutton">
                         <button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addform2"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _ADDDOMAIN; ?></button>
                          </div><br /><br />
                          <?php
                          status(_DOMAINSINFO."<br><b>".$settings['ns_domain']."</b>", 'info');
                          ?>
  <table class="table">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _DOMAIN; ?></th>
                                            <th><?php echo _UPDATE; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                     if ($domains!=false) {
                                      foreach ($domains as $key => $value) {
                                        if ($value['ssl_ready']!=1) $status = "<span class=\"small green\">SSL generating...</span>"; else $status='';
                                        
                                      	 echo "<tr>
                                            <td>".$key."</td>
                                            <td><a href=https://".$value['domain']." target=_blank>".$value['domain']."</a> ".$status."</td>
                                            <td>".$value['updated']."</td>
                                            <td>
                                            <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#deldomain".$key."\">"._DELETE."</button><br />
                                            <div id=\"block-".$key."\"></div></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
<?php } ?>
</div>

</div>


<script src="vendors/chosen/chosen.jquery.min.js"></script>
  <script>
    jQuery(document).ready(function() {
        jQuery(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "450px"
        });
    });
</script>

                <?php
                } else {
                status(_NOACCESS, 'warning');
                }
                  ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

 </div>
  </div>
    </div><!-- /#right-panel -->

    <!-- Right Panel -->


 <?php

         if ($api_keys!=false) {
             foreach ($api_keys as $key => $value) {
              modal(_DELTOKEN." ".$value['name'], _DELTOKEN." ".$value['name']."? <input name=\"delete\" type=\"hidden\" value=\"".$key."\">", 1, "del".$key, "?m=account");
              if ($value['status']==1) $ch = "checked";
              $form = "<table><tbody>
                       <tr><td><b>"._NAME."</b></th><td><input name=\"keyname\" type=\"text\" value=\"".$value['name']."\" class=longinput></td> </tr>
                       <tr><td>"._STATUSON."</th><td><input name=\"keystatus\" type=\"checkbox\" value=\"1\" ".$ch."></td></tr>
                       </tbody>
                       </table><input name=\"edit\" type=\"hidden\" value=\"".$key."\">";
              modal(_EDITTOKEN." ".$value['name'], $form, 2, "edit".$key, "?m=account");

              }
              }
       if ($domains!=false) {
             foreach ($domains as $key => $value) {
              modal(_DELDOMAIN, _DELDOMAIN." ".$value['domain']."? <input name=\"deletedomain\" type=\"hidden\" value=\"".$key."\">", 1, "deldomain".$key, "?m=account&tab=5");

              }
              }
      ?>
      <div class="modal fade" id="addform2" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _ADDDOMAIN; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=account&tab=5" method="post">
                               <table>
                                    <tbody>
                                        <tr><td class=col1><b><?php echo _DOMAIN; ?></b></th><td><input name="domain" type="text" value="" class=longinput></td> </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="adddomain" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                

            <script>
                var tabActive = "<?= $tab ?>";
                $('#nav-'+tabActive+'-tab').click();
                $(document).ready(function () {
                    //$('#nav-'+tabActive+'-tab').click();
                });
            </script>
