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

$save = intval($_POST['save']);
$back = intval($_POST['back']);
$ids = $_POST['ids'];
$payments_type = payments_type();
$getlist = intval($_GET['getlist']);
// получение списка на выплаты
if ($getlist==1) {
  
$sql = "SELECT a.id, a.score_id, b.summa  FROM admins AS a INNER  JOIN balance AS b ON (a.id=b.admin_id) 
  WHERE a.auto_money=1 AND a.score_id!=0 AND b.summa >= '".$settings['minsumma_checkout']."'";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();
   $all=0;
   
if (is_array($info)) {
    foreach ($info as $key => $value) {
        list($score, $payment_id) = $db->sql_fetchrow($db->sql_query("SELECT score, payment_id FROM admins_score WHERE id=".$value['score_id'].""));
         if ($score) {
                         $comission = $payments_type[$payment_id]['comission'];
                                    if ($comission) {
                                     $proc = 100 - $comission;    
                                     $summa_out = round($value['summa'] / 100 * $proc, 2);   
                                    }
                                    $db->sql_query("UPDATE balance SET summa=summa-".$value['summa'].", last_edit=now() WHERE admin_id='".$value['id']."'");    
                                    $balance = balance($value['id']);
                                    $check = md5($summa_out.$config['global_secret']);
                                   $db->sql_query("INSERT INTO payment (id, create_time, admin_id, type, payment_type, summa, ostatok, score, spisano, checksum) VALUES 
  (NULL, now(), '".$value['id']."', '3', '".$payment_id."', '".$summa_out."', '".$balance."', '".$score."', '".$value['summa']."', '".$check."')"); 
$all++;
         }                       
    }
    }
    
    if ($all>0) {
    status(_PAYM_ADDED.": $all", 'success');
    } else {
        status(_PAYM_NOUSER, 'warning');
    }
}

if ($ids && $save==1 && $check_login['role']==1) {
    $all_users = count($ids);
$ids = implode(',', $ids);

$payments_messag = payments("AND id IN (".$ids.") AND status=0");
  if (is_array($payments_messag)) {  
    foreach ($payments_messag as $key => $value) {
   
   $user_lang = get_onerow('value', 'settings', "admin_id=".$value['admin_id']." AND name='lang'"); 
   if ($user_lang=='ru') {
    $message = "Произведена выплата средств ".$value['summa']."$ на ".$value['score']."";
   } else {
    $message = "Payment made ".$value['summa']."$ on ".$value['score']."";
   }
        
alert($message, $value['admin_id'], 'success');
$allsumma += $value['summa'];
        
   }
$db->sql_query("UPDATE payment SET status='1', update_time=now() WHERE id IN (".$ids.")");    

jset($check_login['id'], _WITHDROWALDONE.", users: $all_users, summa: $allsumma ");
if ($check_login['id']!=$check_login['getid']) {
alert(_WITHDROWALDONE.", users: $all_users, summa: $allsumma  (user: ".$check_login['login'].")", $check_login['getid']);
}

status(_WITHDROWALDONE, 'success');
} 
}

// отмена выплаты и возврат средств на баланс
if ($ids && $back==1 && $check_login['role']==1) {
    $all_users = count($ids);
    $allsumma=0;
    $ids = implode(',', $ids);
$payments_back = payments("AND id IN (".$ids.") AND status=0");
  if (is_array($payments_back)) {  
    
    foreach ($payments_back as $key => $value) {
  
     $user_lang = get_onerow('value', 'settings', "admin_id=".$value['admin_id']." AND name='lang'"); 
   if ($user_lang=='ru') {
    $message = "Отмена выплаты ".$value['summa']."$ на ".$value['score']."";
   } else {
    $message = "Payment canceled ".$value['summa']."$ on ".$value['score']."";
   }

alert($message, $value['admin_id'], 'success');       
$db->sql_query("UPDATE balance SET summa=summa+".$value['spisano'].", last_edit=now() WHERE admin_id=".$value['admin_id']."");

   $allsumma += $value['spisano'];
    }
    
$db->sql_query("UPDATE payment SET status='3', update_time=now() WHERE id IN (".$ids.")");// or $stop = mysqli_error();

jset($check_login['id'], _WITHDROWALBACK.", users: $all_users, summa: $allsumma ");
if ($check_login['id']!=$check_login['getid']) {
alert(_WITHDROWALBACK.", users: $all_users, summa: $allsumma  (user: ".$check_login['login'].")", $check_login['getid']);
}

status(_WITHDROWALBACK, 'success');
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
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
                         
                         <a href="?m=a_payment&type=out&getlist=1" class="btn btn-success"><i class="fa  fa-level-down"></i> <?php echo _GET_WITHDR ?></a>

                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php


                             $admin_id = intval($_GET['admin_id']);
                             if ($admin_id==0) $admin_id ='';
                             $payment_type  = intval($_GET['payment_type']);
                             $sum_from = text_filter($_GET['sum_from']);
                             $sum_to = text_filter($_GET['sum_to']);     
                                     

                                     if ($payment_type) {
                                     $where .= "AND payment_type = '$payment_type'  ";
                                     $dopurl .= "&payment_type=".$payment_type."";
                                     }
                                     if ($sum_from && $sum_to) {
                                      $where = "AND summa >= '".$sum_from."' AND summa <= '".$sum_to."' ";
                                     $dopurl = "&sum_from=".$sum_from."&sum_to=".$sum_to."";  
                                     } elseif ($sum_from) {
                                      $where = "AND summa >= '".$sum_from."'  ";
                                     $dopurl = "&sum_from=".$sum_from."";  
                                     }elseif ($sum_to) {
                                      $where = " AND summa <= '".$sum_to."' ";
                                     $dopurl = "&sum_to=".$sum_to."";  
                                     } 


                                      $where .= "AND type=3 AND status=0";
                                      $payments = payments($where, 'id');
                                 
                                      if ($all_payments) $all_payments = count($payments); else $all_payments = 0;
                                      
                                      $admins = admins();
                                      
                                      $admins_score = admins_score();
                                      if (is_array($admins_score)) {
                                        $admins_checkscore=array();
                                        foreach ($admins_score as $key => $value) {
                                        $admins_checkscore[$value['admin_id']][$value['score']] = $value['check_code'];
                                        }
                                      }
                                     ?>
                              <div class="row form-group">
                                    <div class="col col-md-2"><?php echo _PAYMENTTYPE ?> <select name="payment_type" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                    <?php 
                                    foreach ($payments_type as $key => $value) {
                                        if ($payment_type && $key==$payment_type) $sel ='selected'; else $sel='';
                                     echo "<option value=\"$key\" ".$sel.">".$value['title']."</option>";
                                     }
                                   ?>
                                    </select>
                                    </div>
                                    <div class="col col-md-2 inlineblock"><?php echo _SUMMA ?><br />
                                   <input type="text" name="sum_from" value="<?php echo $sum_from ?>"  placeholder="<?php echo _FROM ?>" class="form-control form-control-sm col col-md-4"> 
                                   <input type="text" name="sum_to" value="<?php echo $sum_to ?>"  placeholder="<?php echo _TILL ?>" class="form-control form-control-sm col col-md-4">
                                   </div>
                                   </div> 

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                 <input name="type" type="hidden" value="out">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
 <script type="text/javascript">
                                 $('#datepicker').flatpickr({
                                     enableTime: true,
                                     dateFormat: "Y-m-d H:i",
                                     time_24hr: true,
                                     locale: "<?php echo $lang ?>"
                                 });
                                  $('#datepicker2').flatpickr({
                                      enableTime: true,
                                      dateFormat: "Y-m-d H:i",
                                      time_24hr: true,
                                      locale: "<?php echo $lang ?>"
                                 });
                                   </script> 
                                  <script type="text/javascript">
function setChecked(obj)
   {

   var check = document.getElementsByName("ids[]");
   for (var i=0; i<check.length; i++)
      {
      check[i].checked = obj.checked;
      }

	var check2 = document.getElementsByName("users[]");
   for (var i=0; i<check2.length; i++)
      {
      check2[i].checked = obj.checked;
      }
   }
</script>

                            <div class="card-body">
                            <form action="index.php?m=<?php echo $module; ?>" method="post">
                            <label><?php echo _CHOSENALL; ?> <input type="checkbox" name="set" onclick="setChecked(this)" /></label>
                                <table class="table">
                                    <thead>
                                        <tr>
                                      <th width=5%>#</th>
                                            <th width=15%><?php echo _TIME; ?></th>
                                            <th width=15%>user</th>
                                            <th><?php echo _PAYMENTTYPE; ?></th>
                                            <th><?php echo _SUMMA; ?></th>
                                            <th><?php echo _BALANCE; ?></th>
                                            <th><?php echo _COMMENT; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                      if ($payments!=false) {
                                        $num=1;
                                        $thedate = date("Y-m-d");
                                      foreach ($payments as $key => $value) {
                                        
                                        if ($value['payment_type']!=0) $payment_type = $payments_type[$value['payment_type']]['title']; else $payment_type = '-';
                                      	if ($value['score']) $score = "(".$value['score'].")"; else $score='';
                                        $login = $admins[$value['admin_id']]['login'];
                                        $status = $admins[$value['admin_id']]['status'];
                                         $balance = balance($value['admin_id']);
                                         $check_code = md5($value['score'].$config['global_secret']);
                                         $stop='';
                                         if ($status!=1){
                                            $stop = _USERBLOCKED."<br>";
                                         }
                                         if (!$admins_checkscore[$value['admin_id']][$value['score']]) {
                                            $stop .= _ASCOREERROR."<br>";
                                         } elseif ($admins_checkscore[$value['admin_id']][$value['score']]!=$check_code) {
                                            $stop .= _ACHECKCODE_ERROR." <br>";
                                         }
                                         $checksum = md5(round($value['summa'], 2).$config['global_secret']);
                                         if ($checksum!=$value['check']) $stop .= _CHECKSUMWRONG."<br>";
                                         
                                         if (!$stop) {
                                         $check = "<input name=\"ids[]\" type=\"checkbox\" value=\"$key\">";
                                         $csv_content[$value['payment_type']] .= "".$value['score']."; ".$value['summa']."; ".$settings['siteurl'].", $thedate, pay ".$value['summa']." for $login; $num\n";
                                         $stop='-';
                                         } else {
                                            $stop = "<span class=red>$stop</span>";
                                            $check='';
                                         }
                                         
                                         
                                           echo "<tr>
                                        <td>".$key." ".$check."</td>
                                        <td>".$value['create_time']."</td>
                                        <td><a href=?m=$module&admin_id=".$value['admin_id'].">#".$value['admin_id']."</a> (<a href=?m=a_users&admin_id=".$value['admin_id']." target=_blank>".$login."</a>)</td>
                                        <td>".$payment_type." ".$score."</td>
                                        <td><strong>".$value['summa']."$</strong></td>
                                        <td>".$balance."$</td>
                                        <td>".$stop."</td>
                                        </tr>";
                                        
                                        $payments_types[$value['payment_type']]['summa'] += $value['summa'];
                                        $payments_types[$value['payment_type']]['all'] += 1;
                                        
                                        
                                        $num++;
                                      }
                                      if (is_array($csv_content)) {
                                      foreach ($csv_content as $key => $value) {
                                      $file = "files/logs/pay_".$thedate."_".$key.".csv";
                                      $type_files[$key] = $file;
                                      $fp = fopen($file, "w");
                                      fwrite($fp, "$value");
                                      fclose($fp);
                                      }}

                                     }
                                    ?>
                                    </tbody>
                                </table>
                                <ul style="padding-left: 26px !important;">
                                 <?php
                                  if ($payments_types!=false) {
                                     foreach ($payments_types as $key => $value) {
                                       echo "<li>".$payments_type[$key]['title']. "- <strong>".$value['summa']."$</strong> (".$value['all'].") - <a href=\"/".$type_files[$key]."\" target=_blank>"._LOADLIST."</a></li>"; 
                                     }     
                                  }      
                                        
                                  ?>
                               </ul> 
    <br><a href=https://masspayment.wmtransfer.com target=_blank><?php echo _MASSPAYMENT; ?></a>
<br><br>
<button type="submit" value="1" name="save" class="btn btn-success"><?php echo _DONEPAY; ?></button> <br><br>
<button type="submit" value="1" name="back" class="btn btn-danger"><?php echo _DELETEPAY; ?></button>
<input type="hidden" name="type" value="<?php echo $type; ?>" />
                                </form>                           
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

