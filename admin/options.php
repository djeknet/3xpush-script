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
if ($check_login['root']!=1) exit;
?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _OPTIONS ?></h4>
</div>
         <?php
$type = intval($_GET['type']);
$id = intval($_GET['id']);
$status = intval($_REQUEST['status']);
$save = intval($_POST['save']);
$name = text_filter($_POST['name']);
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;
$edit_form = intval($_GET['edit']);
$save_payments = intval($_POST['save_payments']);
$title = text_filter($_POST['title']);
$minsumma = intval($_POST['minsumma']);
$score = text_filter($_POST['score']);
$comission = text_filter($_POST['comission']);
$edit = text_filter($_POST['edit']);
$withdrowal = intval($_POST['withdrowal']);
$autorefill = intval($_POST['autorefill']);
$texts = $_POST['texts'];

if ($save_payments) {
  if (!$title || !$minsumma || !$score) $stop = _INSTALL21;

foreach ($texts as $key => $value) {
$value= text_filter($value, 2);
$value = str_replace("\r\n", "<br>", $value);
$contents[$key] = $value;
}
$contents = json_encode($contents, JSON_UNESCAPED_UNICODE);

          if (intval($_FILES['img']['size'])){
             $directory = "../img";
             foreach ($_FILES as $key => $val) {
            if ($val['size']==0) continue;
            $filename = upload($directory, $imgtype, 800000, "", 2000, 2000, $key);
            $img = "".$directory."/".$filename."";
            $newimg = "logo='$img', ";
        }
        }

  if (!$stop) {
    if ($edit) {
       $db->sql_query("UPDATE payment_type SET ".$newimg." texts='$contents', withdrowal='$withdrowal', autorefill='$autorefill', title='$title', status='$status', minsumma='$minsumma', score='$score', comission='$comission' WHERE id=$edit");
       status(_UPDATEFIELD, 'success');
    } else {
        $db->sql_query("INSERT INTO payment_type (id, title, status, minsumma, score, comission, logo, withdrowal, autorefill, texts) VALUES (NULL, '$title', '$status', '$minsumma', '$score', '$comission', '$img', '$withdrowal', '$autorefill', '$contents')");
        status(_INSERTFIELD, 'success');
    }

  }  else {
    status($stop, 'danger');
  }
}

if ($type=='payments' && $id && $check_login['role']==1) {

 $db->sql_query("UPDATE payment_type SET status='$status' WHERE id=$id");
 status(_UPDATEFIELD, 'success');
}

if ($save==1 && $check_login['role']==1) {
foreach ($_POST['options'] as $key => $value) {

    if ($key=='timezone') {
      $values = explode("_", $value);

      $value = $values[0];
      $zone_diff = $values[1];
      $db->sql_query("SET GLOBAL time_zone = '$zone_diff'");
    }
    if ($key=='firebase_conf') {
    $value = str_replace("firebaseConfig", "config", $value);       
     }

     if ($key=='black_ip' || $key=='ns_domain') {
     $value = str_replace("\r\n", ",", $value);   
        }


  $db->sql_query("UPDATE settings SET value='$value' WHERE name='$key' and admin_id=0") or die ("<center><br>".mysql_error()."</center>");
    }
    
    $checkboxes = array('check_inputs_blockip','check_inputs_blockuser','check_inputs_alert','check_inputs','allow_admins','allow_options','allow_domains','referal_manual','allow_referal','email_confirm','register_on','dont_send_after_click','traf_exchange_on','sending_on','get_feeds','check_url','image_send','enable_messaging', 'block_unsubs', 'block_ctr', 'test','allow_copyland', 'captcha_register','captcha_login');
    foreach ($checkboxes as $key => $value) {
    if ($_POST['options'][$value]!=1) {
   $db->sql_query("UPDATE settings SET value='0' WHERE name='$value' and admin_id=0");
    }
    }

    
    status(_OPTIONS23, 'success');
    }


$sql = "SELECT * FROM settings WHERE admin_id=0";
$settings1 = $db->sql_query($sql);
$settings1 = $db->sql_fetchrowset($settings1);
   foreach ($settings1 as $key1 => $val) {
   $settings[$val['name']] = $val['value'];
   if ($val['value']==1) {
    $checked[$val['name']] = 'checked';
   }
   }


if ($settings['sorttype']) {
$sortsel[$settings['sorttype']] = "checked";
} else $sortsel[1] = "checked";
if ($settings['myadssort']) {
$myadssortsel[$settings['myadssort']] = "checked";
} else $myadssortsel[1] = "checked";

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

$settings['black_ip'] = str_replace(",", "\n", $settings['black_ip']);
$settings['ns_domain'] = str_replace(",", "\n", $settings['ns_domain']);
                  ?>

<nav>
<div class="nav nav-tabs" id="nav-tab-1" role="tablist">
<a class="nav-item nav-link <?= $tab==1 ? 'active' : '' ?>" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-1" aria-selected="true" name="1"><?php echo _COMMONS; ?></a>
<a class="nav-item nav-link <?= $tab==2 ? 'active' : '' ?>" id="nav-2-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-2" aria-selected="false" name="2"><?php echo _FINANCE; ?></a>
<a class="nav-item nav-link <?= $tab==4 ? 'active' : '' ?>" id="nav-4-tab" data-toggle="tab" href="#nav-4" role="tab" aria-controls="nav-3" aria-selected="false" name="4" onclick="document.location.href = 'index.php?m=options&tab=4'"><?php echo _AAOPTIONS12; ?></a>
<a class="nav-item nav-link <?= $tab==5 ? 'active' : '' ?>" id="nav-5-tab" data-toggle="tab" href="#nav-5" role="tab" aria-controls="nav-3" aria-selected="false" name="5" onclick="document.location.href = 'index.php?m=options&tab=5'"><?php echo _AAOPTIONS16; ?></a>
<a class="nav-item nav-link <?= $tab==10 ? 'active' : '' ?>" id="nav-10-tab" data-toggle="tab" href="#nav-10" role="tab" aria-controls="nav-10" aria-selected="false" name="10" onclick="document.location.href = 'index.php?m=options&tab=10'"><?php echo _AAOPTIONS14; ?></a>
</div>
</nav>
  <div class="tab-content pl-3 pt-2" id="nav-tabContent-1">
<div class="tab-pane fade show <?= $tab==1 ? 'active' : '' ?>" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab">
 <form name="addsite" action="?m=options" method="post">
                               <table class=table1 width=100%>
                                    <tbody>
<tr><td colspan="2"><b><?php echo _COMMON; ?></b></td></tr>
<tr><td width=30%><?php echo _AOPTIONS9; ?></td><td><input name="options[sitename]" type="text" value="<?php echo $settings['sitename'] ?>"> </td></tr>
<tr><td><?php echo _AOPTIONS13; ?></td><td><input name="options[siteurl]" type="text" value="<?php echo $settings['siteurl'] ?>"> </td></tr>
<tr><td><?php echo _AOPTIONS10; ?></td><td><input name="options[support_mail]" type="text" value="<?php echo $settings['support_mail'] ?>"> </td></tr>
<tr><td><?php echo _AOPTIONS11; ?></td><td><input name="options[from_mail]" type="text" value="<?php echo $settings['from_mail'] ?>"> </td></tr>
<tr><td><?php echo _AAOPTIONS1; ?></td><td><input name="options[system_mail]" type="text" value="<?php echo $settings['system_mail'] ?>"> </td></tr>
<tr><td>Telegram</td><td><input name="options[telegram]" type="text" value="<?php echo $settings['telegram'] ?>"> </td></tr>
<tr><td><?php echo _AAOPTIONS2; ?></td><td><input name="options[sending_on]" type="checkbox" value="1" <?php echo $checked['sending_on']; ?>>  <?php echo tooltip(_SENDING_TOOLTIP); ?></td></tr>
<tr><td><?php echo _AAOPTIONS3; ?></td><td><input name="options[traf_exchange_on]" type="checkbox" value="1" <?php echo $checked['traf_exchange_on']; ?>> <?php echo tooltip(_TREX_TOOLTIP); ?></td></tr>
<tr><td><?php echo _ALLOW_COPYLAND; ?></td><td><input name="options[allow_copyland]" type="checkbox" value="1" <?php echo $checked['allow_copyland']; ?>> <?php echo tooltip(_COPYL_TOOLTIP); ?></td></tr>
<tr><td><?php echo _ALLOW_REFERAL; ?></td><td><input name="options[allow_referal]" type="checkbox" value="1" <?php echo $checked['allow_referal']; ?>> <?php echo tooltip(_ALLOWREF_TOOLTIP); ?></td></tr>
<tr><td><?php echo _REFERAL_MUAL; ?></td><td><input name="options[referal_manual]" type="checkbox" value="1" <?php echo $checked['referal_manual']; ?>> <?php echo tooltip(_REFACTIVE_TOOLTIP); ?></td></tr>
<tr><td><?php echo _ALLOW_DOMAINS; ?></td><td><input name="options[allow_domains]" type="checkbox" value="1" <?php echo $checked['allow_domains']; ?>> <?php echo tooltip(_ALLOWDOMAINS_TOOLTIP); ?></td></tr>
<tr><td><?php echo _ALLOW_ACCTOUNTS; ?></td><td><input name="options[allow_admins]" type="checkbox" value="1" <?php echo $checked['allow_admins']; ?>> <?php echo tooltip(_ALLOW_ACCTOUNTS_TOOLTIP); ?></td></tr>
<tr><td><?php echo _ALLOW_OPTIONS; ?></td><td><input name="options[allow_options]" type="checkbox" value="1" <?php echo $checked['allow_options']; ?>> <?php echo tooltip(_ALLOW_OPTIONS_TOOLTIP); ?></td></tr>
<tr><td><?php echo _EXCHANGE_MIN; ?></td><td><input name="options[exchange_min]" type="text" value="<?php echo $settings['exchange_min'] ?>"> <?php echo tooltip(_EXCHMIN_TOOLTIP); ?> </td></tr>
<tr><td><?php echo _NS_DOMAIN; ?></td><td><textarea name="options[ns_domain]" rows=5 cols=50 wrap="off"><?php echo $settings['ns_domain']; ?></textarea>  <?php echo tooltip(_DOMAINNS_TOOLTIP); ?></td></tr>
<tr><td><?php echo _SYS_LANGS; ?></td><td><input name="options[langs]" type="text" value="<?php echo $settings['langs'] ?>"> <?php echo tooltip(_LANGS_TOOLTIP); ?> </td></tr>
<tr><td colspan="2"><b><?php echo _AAOPTIONS4; ?></b></td></tr>
<tr><td><?php echo _AAOPTIONS5; ?></td><td><input name="options[register_on]" type="checkbox" value="1" <?php echo $checked['register_on']; ?>>  <?php echo tooltip(_REGISTERON_TOOLTIP); ?></td></tr>
<tr><td><?php echo _AAOPTIONS6; ?></td><td><input name="options[email_confirm]" type="checkbox" value="1" <?php echo $checked['email_confirm']; ?>> <?php echo tooltip(_EMAILCONFIRM_TOOLTIP); ?></td></tr>
<tr><td><?php echo _AAOPTIONS7; ?></td><td><input name="options[captcha_register]" type="checkbox" value="1" <?php echo $checked['captcha_register']; ?>>  <?php echo tooltip(_REGCAPTCHA_TOOLTIP); ?></td></tr>
<tr><td><?php echo _AAOPTIONS8; ?></td><td><input name="options[captcha_login]" type="checkbox" value="1" <?php echo $checked['captcha_login']; ?>> <?php echo tooltip(_LOGCAPTCHA_TOOLTIP); ?></td></tr>
<tr><td><?php echo _AAOPTIONS9; ?></td><td><textarea name="options[black_ip]" rows=5 cols=50 wrap="off"><?php echo $settings['black_ip']; ?> </textarea><?php echo tooltip(_BLACKIP_TOOLTIP); ?></td></tr>
<tr><td>Google Recaptcha Secret</td><td><input name="options[google_recaptcha]" size=100 type="text" value="<?php echo $settings['google_recaptcha'] ?>"> <?php echo tooltip(_RECAPTCHA_TOOLTIP); ?></td></tr>
<tr><td>Google Recaptcha Public</td><td><input name="options[google_recaptcha_public]" size=100 type="text" value="<?php echo $settings['google_recaptcha_public'] ?>"> <?php echo tooltip(_RECAPTCHA_TOOLTIP); ?></td></tr>
<tr><td colspan="2"><b><?php echo _SEND_PUSH; ?></b></td></tr>
<tr ><td ><?php echo _OPTIONS1; ?></td><td><input name="options[enable_messaging]" type="checkbox" value="1" <?php echo $checked['enable_messaging']; ?>> <?php echo tooltip(_SENDINGPUSH_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS2; ?></td><td><input name="options[send_every]" type="text" value="<?php echo $settings['send_every'] ?>"> <?php echo _H; ?>  <?php echo tooltip(_SEND_EVERY_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERCREATE; ?></td><td><input name="options[send_fornew]" type="text" value="<?php echo $settings['send_fornew'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_FORNEW_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERVIEW; ?></td><td><input name="options[send_afterview]" type="text" value="<?php echo $settings['send_afterview'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_AFTERVIEW_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS_AFTERCLICK; ?></td><td><input name="options[send_afterclick]" type="text" value="<?php echo $settings['send_afterclick'] ?>"> <?php echo _H; ?> <?php echo tooltip(_SEND_AFTERCLICK_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS3; ?></td><td><input name="options[user_messages]" type="text" value="<?php echo $settings['user_messages'] ?>"> <?php echo _OPTIONS18; ?> <input name="options[user_messages_days]" type="text" value=<?php echo $settings['user_messages_days'] ?>> days <?php echo tooltip(_USER_MESAGES_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS4; ?></td><td><input name="options[max_send]" type="text" value="<?php echo $settings['max_send'] ?>"> <?php echo _OPTIONS19; ?> <?php echo tooltip(_MAXSEND_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS5; ?></td><td><input name="options[max_adv_send]" type="text" value="<?php echo $settings['max_adv_send'] ?>"> <?php echo _OPTIONS19; ?> <?php echo tooltip(_MAXSEND_ADV_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS6; ?></td><td><input name="options[dont_send_after_click]" type="checkbox" value="1" <?php echo  $checked['dont_send_after_click']; ?>> <?php echo tooltip(_DNTSENDAFTRCL_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS26; ?></td><td><input name="options[check_url]" type="checkbox" value="1" <?php echo $checked['check_url']; ?>>  <?php echo tooltip(_CHECKURL_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS25; ?></td><td><input name="options[image_send]" type="checkbox" value="1" <?php echo $checked['image_send']; ?>> <?php echo tooltip(_IMAGESND_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS7; ?></td><td><input name="options[mass_mess_count]" type="text" value="<?php echo $settings['mass_mess_count'] ?>"> <?php echo _OPTIONS20; ?>  <?php echo tooltip(_SENDLIMIT_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS24; ?></td><td><input name="options[time_to_live]" type="text" value="<?php echo $settings['time_to_live'] ?>"> <?php echo _M; ?> <?php echo tooltip(_TIMEVAL_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS8; ?></td><td><input name="options[trunk_title]" type="text" value="<?php echo $settings['trunk_title'] ?>"> <?php echo _OPTIONS21; ?> <?php echo tooltip(_TRNKTITLE_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS9; ?></td><td><input name="options[trunk_description]" type="text" value="<?php echo $settings['trunk_description'] ?>"> <?php echo _OPTIONS21; ?> <?php echo tooltip(_TRNKDESCR_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS10; ?></td><td><input name="options[domain]" type="text" value="<?php echo $settings['domain'] ?>"> <?php echo tooltip(_PUSHDOMAIN_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _CODEDOMAIN; ?></td><td><input name="options[domain_code]" type="text" value="<?php echo $settings['domain_code'] ?>"> <?php echo tooltip(_JSDOMAIN_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _LINKDOMAIN; ?></td><td><input name="options[domain_link]" type="text" value="<?php echo $settings['domain_link'] ?>"> <?php echo tooltip(_LANDDOMAIN_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS11; ?></td><td><input name="options[minstavka]" type="text" value="<?php echo $settings['minstavka'] ?>"> <?php echo tooltip(_MINBID_TOOLTIP); ?></td></tr>
<tr ><td>Server key</td><td><input name="options[server_key]" size=100 type="text" value="<?php echo $settings['server_key'] ?>"> <?php echo tooltip(_SRVKEY_TOOLTIP); ?></td></tr>
<tr ><td>Firebase conf</td><td><textarea name="options[firebase_conf]" rows=5 cols=50 wrap="off"><?php echo $settings['firebase_conf']; ?> </textarea> <?php echo tooltip(_FRBSCONF_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS12; ?></td><td><input name="options[test]" type="checkbox" value="1" <?php echo $checked['test']; ?>> <?php echo tooltip(_TST_TOOLTIP); ?> </td></tr>
<tr ><td><?php echo _OPTIONS13; ?></td><td><input name="options[test_id]" type="text" value="<?php echo $settings['test_id'] ?>"> <?php echo tooltip(_TSTID_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS14; ?></td><td><textarea name="options[stopwords]" rows=5 cols=50 wrap="on"><?php echo $settings['stopwords']; ?></textarea> <?php echo tooltip(_STPWRDS_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS15; ?></td><td><input name="options[block_unsubs]" type="checkbox" value="1" <?php echo $checked['block_unsubs']; ?>> <?php echo tooltip(_BLOCKUNSUBS_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS16; ?></td><td><input name="options[cr_block]" type="text" value="<?php echo $settings['cr_block'] ?>"> <?php echo tooltip(_UNSUBSPROC_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _OPTIONS17; ?></td><td><input name="options[sorttype]" type="radio" value="1" <?php echo $sortsel[1]; ?>> <?php echo _OPTIONS22; ?> &nbsp;&nbsp; <input name="options[sorttype]" type="radio" value="2" <?php echo $sortsel[2]; ?>> ctr &nbsp;&nbsp; <input name="options[sorttype]" type="radio" value="3" <?php echo $sortsel[3]; ?>> cpm <?php echo tooltip(_SORTTYPE_TOOLTIP); ?></td></tr>
<tr ><td><?php echo _FEED_TIMEOUT; ?></td><td><input name="options[feed_timeout]" type="text" value="<?php echo $settings['feed_timeout'] ?>"> sek <?php echo tooltip(_FEED_TIMEOUT_TOOLTIP); ?> </td></tr>
<tr ><td colspan="2"><b><?php echo _FINANCE; ?></b></td></tr>
<tr><td><?php echo _AOPTIONS5; ?></td><td><input name="options[minsumma_checkout]" type="text" value="<?php echo $settings['minsumma_checkout'] ?>"> <?php echo tooltip(_MINSUMMA_TOOLTIP); ?> </td></tr>
<tr><td><?php echo _AAOPTIONS11; ?></td><td><input name="options[feeds_proc]" type="text" value="<?php echo $settings['feeds_proc'] ?>"> <?php echo tooltip(_FPROC_TOOLTIP); ?> </td></tr>
<tr><td colspan="2"><b><?php echo _SECURITY; ?></b></td></tr>
<tr><td><?php echo _SECURITY1; ?></td><td><input name="options[check_inputs]" type="checkbox" value="1" <?php echo  $checked['check_inputs']; ?>></td></tr>
<tr><td><?php echo _SECURITY2; ?></td><td><input name="options[check_inputs_alert]" type="checkbox" value="1" <?php echo  $checked['check_inputs_alert']; ?>></td></tr>
<tr><td><?php echo _SECURITY3; ?></td><td><input name="options[check_inputs_blockuser]" type="checkbox" value="1" <?php echo  $checked['check_inputs_blockuser']; ?>></td></tr>
<tr><td><?php echo _SECURITY4; ?></td><td><input name="options[check_inputs_blockip]" type="checkbox" value="1" <?php echo  $checked['check_inputs_blockip']; ?>></td></tr>
<tr><td><?php echo _SECURITY5; ?></td><td><input name="options[check_inputs_count]" type="text" value="<?php echo $settings['check_inputs_count'] ?>"> </td></tr>
<tr ><td colspan="2"><b><?php echo _STAT; ?></b></td></tr>
<tr><td><?php echo _OPTIONS30; ?></td><td><input name="options[days_stat]" type="text" value="<?php echo $settings['days_stat'] ?>"> </td></tr>
<tr><td><?php echo _OPTIONS31; ?></td><td><select data-placeholder="<?php echo _CHOSEREGION; ?>" name="options[timezone]" class="standardSelect" tabindex="1">
                                    <option value=""></option>
                                        <?php foreach(tz_list() as $t) {
                                         if ($settings['timezone']==$t['zone']) $sel = 'selected';  else $sel='';

                                        echo "<option value=\"".$t['zone']."_".$t['mysql_diff']."\" ".$sel.">
                                        ".$t['diff_from_GMT'] . " - " . $t['zone']."
                                         </option>";
                                         }
                                        ?>
                                    </select>
                                 </td></tr>
                                    </tbody>
                             </table>
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
                                <input name="save" type="hidden" value="1">
                      <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                </form>
  </div>
  <div class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">
 <?php
                           $title_form = _ADD;
                            $icon = 'plus';

                            $currCategories = array();
                            $time_type_sel[0] = "checked";

                            if ($edit_form) {
                                $payments_edit = payments_type("AND id=".$edit_form."");

                                $statussel[$payments_edit[$edit_form]['status']] = "checked";
                                $withdrowalsel[$payments_edit[$edit_form]['withdrowal']] = "checked";
                                $autorefillsel[$payments_edit[$edit_form]['autorefill']] = "checked";
                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                
                                $text_edit = json_decode($payments_edit[$edit_form]['texts'], true);

                            }
                             $texts = array('ru' => '', 'en' => '');
   ?>
 <a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._PAYMETHOD; ?></a>
<form action="?m=options" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">

                <tr><td width="20%"><strong><?php echo _NAME; ?></strong></td><td><input type="text" name="title" value="<?php echo $payments_edit[$edit_form]['title']; ?>" size="30" /></td></tr>
                <tr><td><?php echo _COMISSION; ?></td><td><input type="text" name="comission" value="<?php echo $payments_edit[$edit_form]['comission']; ?>" size="5" /></td></tr>
                <tr><td><strong>Logo</strong></td><td><input type="file" name="img"></td></tr>
                <tr><td><?php echo _STATUS; ?></td><td>
                <input name="status" type="radio" value="1" <?php echo $statussel[1]; ?>> <?php echo _STATUSON; ?> &nbsp;&nbsp;
                <input name="status" type="radio" value="0" <?php echo $statussel[0]; ?>> <?php echo _STATUSOFF; ?></td></tr>
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save_payments" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">
</form>

  <table class=table  width=100%>
<tbody>
     <thead>
      <tr>
       <th>№</th>
     <th><?php echo _NAME; ?></th>
    <th>logo</th>
    <th><?php echo _COMISSION; ?></th>
    <th><?php echo _STATUS; ?></th>
    </tr>
 </thead>
   <tbody>
<?php
$payments_type = payments_type();

if (is_array($payments_type)) {
foreach ($payments_type as $key => $value) {
 if ($value['status']==1) {
    $status = "<span class=green>"._STATUSON."</span><br><a href=?m=options&tab=2&type=payments&id=".$key."&status=2>"._OFF."</a>"; }

 else {$status = "<span class=red>"._STATUSOFF."</span><br><a href=?m=options&tab=2&type=payments&id=".$key."&status=1>"._ON."</a>";
  }


echo "<tr><td width=5%>".$key."</td>
<td>".$value['title']."</td>
<td><img src=".$value['logo']." width=100></td>
<td>".$value['comission']."%</td>
<td>".$status."<br><br>
 <a href=?m=options&tab=2&edit=".$key.">"._EDIT."</a></td>
</tr>";
}
}
?>
  </tbody>
</table>

<script>
$("#show_form").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });
                        </script>
   </div>


      <div class="tab-pane fade show <?= $tab==4 ? 'active' : '' ?>" id="nav-4" role="tabpanel" aria-labelledby="nav-4-tab">

      <?php

      require_once 'models/LangsModel.php';
      require_once 'models/LandingsModel.php';

      $pagenum = isset($_GET['page']) ? intval($_GET['page']) : 1;
      $filenum = 5;

      $macrosTotal = LangsModel::getInstance()->getMacrosTotal($pagenum);
      $macrosNames = LangsModel::getInstance()->getMacrosNames($pagenum, $filenum);
      $macrosStore = LangsModel::getInstance()->getMacros($macrosNames);

      ?>

          <form name="search_macros_form" id="search_macros_form" method="post">
              <div class="row form-group">
                  <div class="col col-lg-2">
                      <label><?php echo _MACROS; ?></label>
                      <input type="text" name="search_macros" value="" class="form-control form-control-sm" />
                  </div>
                  <div class="col col-lg-2">
                      <label><?php echo _TEXT; ?></label>
                      <input type="text" name="search_text" value="" class="form-control form-control-sm" />
                  </div>
              </div>
              <div class="row">
                  <div class="col col-lg-2">
                      <button class="btn btn-primary btn-sm"><i class="fa fa-search"></i> <?php echo _SEARCH; ?></button>
                      <button type="button" class="btn btn-danger btn-sm" id="resetButton"><i class="fa fa-ban"></i> <?php echo _RESET; ?></button>
                  </div>
                  <div class="col col-lg-2">

                  </div>
              </div>
          </form>

        <form name="add_lang" id="add_lang" method="post" data-action="/admin/ajax/">

            <div style="margin: 20px 0;">
                <label><?php echo _ADD_MACROS; ?></label>
                <div>
                    <input type="text" name="name" id="macros_name" value="" />
                    <button class="btn btn-primary btn-sm" type="button" id="add_macros"><?php echo _SEND; ?></button>
                </div>
            </div>
            <table class="table" id="langTable">
                <thead>
                    <tr>
                        <th><?php echo _MACROS; ?></th>
                        <th><?php echo _AAOPTIONS15; ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <?php
            $numpages = ceil($macrosTotal / $filenum);
            $dopurl='&tab=4';
            num_page(array(), $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
            ?>
            <div>
                <button id="btn_save"><?php echo _SAVE; ?></button>
            </div>
        </form>

      <style>
          .lang_form_row {
              padding-bottom: 10px;
          }

          .macros_column_name {
              text-align: center;
          }
      </style>

      <?php
      $macrosLandings = LandingsModel::getInstance()->findMacros($macrosStore);
      ?>

      <script type="text/javascript">
          <?php

          ?>
          var macrosStore = <?= json_encode($macrosStore); ?>;
          var macrosLandings = <?= json_encode($macrosLandings); ?>;

          $(document).ready(function () {

              $.each(macrosStore, function (name, item) {
                  var content = createRow(name, item);
                  $('#langTable tbody').append(content);
              });

              function getRowIndex() {
                  return $('#langTable tbody tr').length + 1;
              }

              function createRow(name, item) {
                  var index = getRowIndex();
                  var content = '<tr data-index="' + index + '">';

                  var macrosUse = [];
                  if(typeof macrosLandings[name] !== 'undefined') {
                      macrosUse = macrosLandings[name];
                  }

                  var landUseHtml = '<div>used: <br/>';

                  $.each(macrosUse, function (k, v) {
                      landUseHtml += '<div><a href="/land.php?lid=' + v + '&test=1">land_' + v + '</a></div>';
                  });
                  landUseHtml += '</div>';

                  var input = '<input type="hidden" name="name[' + index + ']" value="' + name + '" />';
                  content += '<td class="macros_column_name">[' + name + ']'+ input + landUseHtml + '</td>';

                  if (typeof item == 'undefined') {
                      content += '<td>' + createBoxEmpty(index) + '</td>';
                  } else {
                      content += '<td>' + createBox(index, item) + '</td>';
                  }

                  content += '</tr>';
                  return content;
              }

              function createBoxEmpty(index) {
                  return createBox(index, [
                      {
                          lang: '',
                          text: ''
                      }
                  ])
              }

              function createBox(index, item) {
                  var content = '<div class="form_lang_box">';
                  $.each(item, function (k, v) {
                      content += createLangRow(index, v);
                  });
                  content += '</div>';
                  content += '<button type="button" class="btn btn-primary btn-sm btn_add_row">&nbsp;+&nbsp;</button>';
                  return content;
              }

              function createLangRow(index, item) {
                  var row = '<div class="lang_form_row">\n';
                  row += '<input type="text" name="lang[' + index + '][]" value="' + item.lang + '" placeholder="lang">\n';
                  row += '<input type="text" name="text[' + index + '][]" value="' + item.text + '" class=longinput placeholder="text" />\n';
                  row += '</div>';
                  return row;
              }

              $(document).on('click', '#add_macros', function () {
                  var name = $("#macros_name").val();

                  if (!name) {
                      alert('Enter macros name');
                      return false;
                  }
                  ;

                  $.ajax({
                      url: 'ajax/lang.php?action=check_macros&name='+name,
                      success: function (response) {
                          response = JSON.parse(response);

                          if(response.success) {
                              var row = createRow(name);
                              $('#langTable tbody').prepend(row);
                          }
                          else {
                              alert(response.messages);
                          }
                      }
                  })


              });

              $(document).on('click', '.btn_add_row', function () {
                  var container = $(this).closest('td');
                  var index = container.closest('tr').data('index');

                  var row = createLangRow(index, {
                      lang: '',
                      text: ''
                  });
                  container.find('.form_lang_box').append(row);
              });

              $("#add_lang").on('submit', function (e) {
                  e.preventDefault();

                  var data = $(this).serializeArray();

                  $.ajax({
                      url: 'ajax/lang.php',
                      type: 'POST',
                      data: data,
                      success: function (response) {
                          response = JSON.parse(response);
                          if(response.success) {
                              document.location.reload(true);
                          }
                      }
                  });

              });

              $('#resetButton').on('click', function (e) {
                  e.preventDefault();
                  document.location.reload(true);
              });

              $('#search_macros_form').on('submit', function (e) {
                  e.preventDefault();

                  var data = $(this).serializeArray();

                  $.ajax({
                      url: 'ajax/lang.php?action=search',
                      type: 'POST',
                      data: data,
                      success: function (response) {
                          response = JSON.parse(response);

                          if(response.success) {

                              $(".numpage").hide();
                              $('#langTable tbody').empty();
                              $.each(response.messages, function (name, item) {
                                  var content = createRow(name, item);
                                  $('#langTable tbody').append(content);
                              });


                          }
                      }
                  });
              });

          });
      </script>

      </div>
      
      <div class="tab-pane fade show <?= $tab==5 ? 'active' : '' ?>" id="nav-5" role="tabpanel" aria-labelledby="nav-5-tab">
<?php
$addfeed = intval($_POST['addfeed']);
$name = text_filter($_POST['name']);
$url = text_filter($_POST['url']);
$feed_title = text_filter($_POST['feed_title']);
$feed_body = text_filter($_POST['feed_body']);
$feed_link_click_action = text_filter($_POST['feed_link_click_action']);
$feed_link_icon = text_filter($_POST['feed_link_icon']);
$feed_link_image = text_filter($_POST['feed_link_image']);
$feed_bid = text_filter($_POST['feed_bid']);
$feed_winurl = text_filter($_POST['feed_winurl']);
$convert_rate = text_filter($_POST['convert_rate']);
$feed_button1 = text_filter($_POST['feed_button1']);
$feed_button2= text_filter($_POST['feed_button2']);
$site = text_filter($_POST['site']);
$status = intval($_POST['status']);
$edit = intval($_POST['edit']);
$params = $_POST['params'];

if ($params) $params = json_encode($params);

$delete = intval($_GET['del']);
if ($addfeed==1 && $check_login['role']==1) {

if (!$name || !$url || !$feed_title || !$feed_body || !$feed_link_click_action || !$feed_link_icon || !$feed_bid) $stop = _FEEDSTEXT1;

if (!$stop) {

$db->sql_query('INSERT INTO feeds_templ (id, name, url,  status, feed_title, feed_body, feed_link_click_action, feed_link_icon, feed_link_image, feed_bid, convert_rate, feed_winurl, site, params, feed_button1, feed_button2)
VALUES (NULL, "'.$name.'", "'.$url.'", '.$status.', "'.$feed_title.'", "'.$feed_body.'", "'.$feed_link_click_action.'", "'.$feed_link_icon.'", "'.$feed_link_image.'", "'.$feed_bid.'", "'.$convert_rate.'", "'.$feed_winurl.'", "'.$site.'", \''.$params.'\', "'.$feed_button1.'", "'.$feed_button2.'")') or $stop = mysqli_error();
if ($stop) {
status($stop, 'danger');
} else {
status(_INSERTFIELD, 'success');
}
} else {
status($stop, 'danger');
}
}

if ($delete && $check_login['role']==1) {
$db->sql_query('DELETE FROM feeds_templ WHERE id='.$delete.'') or $stop = mysql_error();
if ($stop) {
status($stop, 'danger');
} else {
status(_DELETEFIELD, 'success');
}
}

if ($edit && $check_login['role']==1) {
if (!$name || !$url || !$feed_title || !$feed_body || !$feed_link_click_action || !$feed_link_icon || !$feed_bid) $stop = _FEEDSTEXT1;
 if (!$stop) {
   
$db->sql_query('UPDATE feeds_templ SET feed_button1="'.$feed_button1.'", feed_button2="'.$feed_button2.'", params=\''.$params.'\', site="'.$site.'",  name="'.$name.'", url="'.$url.'", status='.$status.', feed_title="'.$feed_title.'", feed_body="'.$feed_body.'", feed_link_click_action="'.$feed_link_click_action.'", feed_link_icon="'.$feed_link_icon.'", feed_link_image="'.$feed_link_image.'", feed_bid="'.$feed_bid.'", convert_rate="'.$convert_rate.'", feed_winurl="'.$feed_winurl.'" WHERE id='.$edit.'') or $stop = mysqli_error();
if ($stop) {
status($stop, 'danger');
} else {
status("#$edit - "._UPDATEFIELD, 'success');
}
} else {
status($stop, 'danger');
}
 }

?>  

 <script type="text/javascript">
                                  function viewblock(id, context) {

                                      if($('#'+id).css('display')=='none') {
                                          $('#'+id).show();

                                          $(context).html('<i class="fa fa-tags"></i> <?php echo _MACROS ?>');
                                      }
                                      else {
                                          $('#'+id).hide();
                                          $(context).html('<i class="fa fa-tags"></i> <?php echo _MACROS ?>');
                                      }
                                }
                                </script>   
   <div class="addbutton">
 <button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addform"  onclick="aj('form_feed.php','0|2|1',1); return false;"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _ADDFEED; ?></button>
</div>   
    <table class="table table-small"  width=100%>
<tbody>
<thead>
<tr>
<th>№</th>
<th><?php echo _NAME; ?></th>
<th><?php echo _SITE; ?></th>
<th>URL</th>
<th><?php echo _OPTIONS; ?></th>
<th><?php echo _ACTIONS; ?></th>
</tr>
</thead>
   <tbody>
<?php
$feeds = feeds_templ('', 'id');

if (is_array($feeds)) {
foreach ($feeds as $key => $value) {
if ($value['status']==1) $status = "<span class=green>"._STATUSON."</span>"; else $status = "<span class=red>"._STATUSOFF."</span>";  
echo "<tr><td width=5%>".$key."</td>
<td>".$value['name']." <br>".$status."</td>
<td><a href=/url.php?u=".$value['site']." target=_blank>".$value['site']."</a></td>
<td><input name=\"Name\" type=\"text\" value=\"".$value['url']."\" size=50></td>
<td><b>title:</b> ".$value['feed_title'].", <b>body:</b> ".$value['feed_body'].", <b>link:</b> ".$value['feed_link_click_action'].",
<b>icon:</b> ".$value['feed_link_icon'].", <b>image:</b> ".$value['feed_link_image'].",  <b>button 1:</b> ".$value['feed_button1'].",  <b>button 2:</b> ".$value['feed_button2'].", <b>winurl:</b> ".$value['feed_winurl'].",
<b>bid:</b> ".$value['feed_bid']."<br />";
if ($value['convert_rate']) echo "<b>"._RATE.":</b> ".$value['convert_rate']."<br />";
echo "</td>
<td><a href=# data-toggle=\"modal\" data-target=\"#edit\" onclick=\"aj('form_feed.php', '".$key."|2|1', 2); return false;\">"._EDIT."</a><br><br><a href=?m=".$module."&tab=5&del=".$key." ".confirm().">"._DELETE."</a></td>
</tr>";
}
}
?>
  </tbody>
</table>   
      
      </div>
    
       <!-- категории сайтов -->
         <div class="tab-pane fade show <?= $tab==10 ? 'active' : '' ?>" id="nav-10" role="tabpanel" aria-labelledby="nav-10-tab">
       
     <?php
        $site_cat_edit = intval($_GET['site_cat_edit']);
        $site_cat_del = intval($_GET['site_cat_del']);
        
        $site_cat_save = intval($_POST['site_cat_save']);
        $edit_save = intval($_POST['edit_save']);
     
     if ($site_cat_save==1) {
$title = $_POST['title'];
foreach ($title as $key => $value) {
$titles[$key] = text_filter($value);
}

$titles = json_encode($titles, JSON_UNESCAPED_UNICODE);

 if (!$stop) {
    if ($edit_save) {
    $db->sql_query("UPDATE sites_category SET titles='".$titles."' WHERE id=".$edit_save."") or $stop = mysqli_error();
  status(_UPDATEFIELD, 'success');
   } else {

     $db->sql_query("INSERT INTO sites_category (id, titles) VALUES (NULL, '$titles')")  or $stop = mysqli_error();
     status(_INSERTFIELD, 'success');
   }
if ($stop) {
status($stop, 'danger');
}
} else {
status($stop, 'danger');
}

     }   
                            $title_form = _ADD;
                            $icon = 'plus';
                            if ($site_cat_edit) {
                                list($title) = $db->sql_fetchrow($db->sql_query("SELECT titles FROM sites_category WHERE id=".$site_cat_edit.""));

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                $titles = json_decode($title, true);
                                
                            } else {
                                $titles = array('ru' => '', 'en' => '');
                            }
                            if (!$date) $date = date("Y-m-d");
   ?>                           
<a href="#" id="show_form43" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._CATEGORY2; ?></a>
<form action="?m=options" method="post" id=form4  enctype="multipart/form-data"<?php echo !$save && !$site_cat_edit ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                
                <tr><td width="20%" valign=top><?php echo _TITLE; ?></td><td>
                 <?php
                 foreach ($titles as $key => $value) {
                 echo "<strong>$key: </strong><input type=\"text\" name=\"title[".$key."]\" value=\"".$value."\" size=\"50\" /><br>";
                 }
                ?>
                </td></tr>   
                </table>
                    <input type="hidden" name="edit_save" value="<?php echo $site_cat_edit; ?>">
                    <input type="hidden" name="site_cat_save" value="1">
                    <input type="hidden" name="tab" value="<?php echo $tab; ?>">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>

         <table class="table">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _NAME; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $category = sites_category();
                                     if ($category!=false) {
                                      foreach ($category as $key => $value) {
                                        $titlestr='';
                                    
                                         foreach ($value['title'] as $lang => $title) {
                                              $titlestr .= "<strong>$lang</strong>: $title<br>";
                                            }

                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$titlestr."</td>                                            
                                            <td><a href=?m=options&tab=10&site_cat_edit=".$key.">"._EDIT."</a><br>
                                            <a href=?m=options&tab=10&site_cat_del=".$key."  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                <script>
$("#show_form43").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });
                        </script>
                           
       </div>       
                  </div>

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


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

 <div class="modal fade" id="addform" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _ADDFEED; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=options" method="post">
                               <div id="block-1">...</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="addfeed" type="hidden" value="1">
                            <input name="tab" type="hidden" value="5">
                              </form>
                        </div>
                    </div>
                </div>
                
            <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _EDITFEED; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=options" method="post">
                               <div id="block-2">...</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="tab" type="hidden" value="5">
                              </form>
                        </div>
                    </div>
                </div>   
                  
                <script>
    jQuery(document).ready(function() {
        jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
         });

 $('#block-1').on('click', '#ajax', function(){
     jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});
 $('#block-2').on('click', '#ajax', function(){
     jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});
$( document ).ajaxStop(function() {
  jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});

</script>