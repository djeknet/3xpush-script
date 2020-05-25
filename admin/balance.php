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
<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _BALANCE ?></h4>
</div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">                      
                    <div class="col-md-12">
                                           <div class="card">
                         <div class="card-body card-block">
                           <?php
                           $success = intval($_GET['success']);
                           $fail = intval($_GET['fail']);
                           $out_sum = intval($_GET['sum']);
                           $balance = balance($check_login['getid']);
                           

                if ($success==1) {
                    status(_PAYSUCCESS, 'success');
                } elseif ($fail==1) {
                    status(_PAYFAIL, 'danger');
                }           
                            $payments_type = payments_type("AND status=1");
                            $payments_type_out = payments_type("AND status=1 AND withdrowal=1");
                        
                            $send = intval($_POST['send']);
                            $score_id = intval($_POST['score_id']);
                            $checkout = intval($_POST['checkout']);
                            $type = intval($_POST['type']);
                            $summa = text_filter($_POST['summa']);
                            $promocod = text_filter($_POST['promocod']);
                            $today = date("Y-m-d");

                            // заказ выплаты
                            if ($checkout==1 && $score_id) {
                              if ($summa > $balance) $stop .= _CHECKOUTERR."<br>";  
                              if ($summa < $settings['minsumma_checkout']) $stop .= _CHECKOUTERR2."<br>";  
                              if ($balance < $settings['minsumma_checkout']) $stop .= _CHECKOUTERR2."<br>";  
                              
                              if (!$stop) {
                                list($score, $payment_id) = $db->sql_fetchrow($db->sql_query("SELECT score, payment_id FROM admins_score WHERE id=".$score_id." AND admin_id=".$check_login['getid'].""));
                                
                                if ($score) {
                                     $comission = $payments_type_out[$payment_id]['comission'];
                                    if ($comission) {
                                     $proc = 100 - $comission;    
                                     $summa_out = round($summa / 100 * $proc, 2);   
                                    }
                                    $old_balance = balance($check_login['getid']);
                                    $db->sql_query("UPDATE balance SET summa=summa-".$summa.", last_edit=now() WHERE admin_id='".$check_login['getid']."'")  or $errors['balance'] = "UPDATE balance SET summa=summa-".$summa.", last_edit=now() WHERE admin_id='".$check_login['getid']."'";  
                                    $new_balance = balance($check_login['getid']);
                                    if ($old_balance==$new_balance) {
                                        
                                    status ("Balance check error! Contact administrator", 'danger');    
                                    
                                    } else {
                                    
                                    $check = md5($summa_out.$config['global_secret']);
                                    
                                   $db->sql_query("INSERT INTO payment (id, create_time, admin_id, type, payment_type, summa, ostatok, score, spisano, checksum) VALUES 
  (NULL, now(), '".$check_login['getid']."', '3', '".$payment_id."', '".$summa_out."', '".$balance."', '".$score."', '".$summa."', '".$check."')") or $errors['payment'] = mysqli_error(); 
  
  jset($check_login['id'], _CHECKOUTSUCCESS);  
  
  if ($check_login['id']!=$check_login['getid']) {
   alert(_CHECKOUTSUCCESS.": ".$summa_out."$ (user: ".$check_login['login'].")", $check_login['getid']); 
  }
                
    status (_CHECKOUTSUCCESS, 'success');
    redir("?m=balance");
    }
                                } else { 
                                    status (_CHECKOUTERR3.": 0002", 'danger');
                                }
                              }else {
                                status ($stop, 'danger');
                              }
                                
                            }
    
                          

                            echo "<strong>"._CHECKOUTRULES." ".$settings['minsumma_checkout']."$</strong><br>";
                            
                            if ($settings['minsumma_checkout'] > $balance) { 
                                status(_CHECKOUTWRONG, 'danger');
                            } else {
                                status(_CHECKOUTRULES2, 'info'); 
                                $admins_score = admins_score("AND admin_id=".$check_login['getid']."");
                                if (is_array($admins_score)) {
                                    foreach ($admins_score as $key => $val) {
                                        if ($val['score']) $isok=1;
                                    }
                                } 
                                if ($isok!=1) {
                                status(_CHECKOUNOSCORE, 'warning');  
                                } else {
                                 if ($check_login['email_check']!=1) { // если почта не проверена, запрещаем вывод
                                  status(_MAILNEEDAPROVE, 'warning');        
                                    } else {
                                   echo  "<form action=\"index.php?m=balance\" method=\"post\">
                                   <p>"._CHECKOUTSUM.":  <input type=\"text\" size=\"5\" maxlength=6 name=\"summa\" value=\"0\"  /> <span class=small>"._AVAIL_BALANCE.": ".$balance."$</span></p>
                                   <p>"._CHECKOUTTYPE.": <select size=\"1\" name=\"score_id\">";
                                    foreach ($admins_score as $key => $val) {
                                        if (!$val['score']) continue;
                                        $payment = $payments_type_out[$val['payment_id']]['title'];
                                        $comission = $payments_type_out[$val['payment_id']]['comission'];
                                        if ($comission) $comission = "(-".$comission."%)"; else $comission = '';
                                         if ($payment) echo "<option value=\"".$key."\">".$payment." - ".$val['score']." ".$comission."</option>";
                                    }
                                   echo "</select></p>
                                   <button type=\"submit\" name=\"checkout\" value=\"1\" class=\"btn btn-primary btn-sm\" style=\"margin-top: -5px;\">"._SEND."</button>
                                   </form> "; 
                                   }
                                }  
                            }     	
                            
                             ?>

                         </div>
                         </div>
                        <div class="card">
                         <div class="card-body card-block">
                         <?php echo "<b>"._PAYHISTORY."</b>"; ?>
<form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $start_date = text_filter($_GET['start_date']);
                             $end_date = text_filter($_GET['end_date']);
                             $operation = intval($_GET['operation']);
                             $pagenum  = intval($_GET['page']);
                             $limit  = intval($_GET['limit']);
                             
                             
                             if (!$pagenum) $pagenum = 1;
                             

                                     if ($start_date && $end_date) {
                                     $where = "AND create_time >= '".$start_date."' AND create_time <= '".$end_date."' ";
                                     $dopurl = "&start_date=".$start_date."&end_date=".$end_date."";
                                     } elseif ($start_date) {
                                     $where = "AND create_time >= '".$start_date."' ";
                                     $dopurl = "&start_date=".$start_date."";
                                     } elseif ($end_date) {
                                     $where = "AND create_time <= '".$end_date."' ";
                                     $dopurl = "&end_date=".$end_date."";
                                     }
                                     if ($operation) {
                                        $operationsel[$operation] = 'selected';
                                        $where .= "AND type=$operation ";
                                        $dopurl .= "&operation=".$operation."";
                                     }
                                     
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      if (!$limit) $limit = 50; else {
                                       $dopurl .= "&limit=".$limit.""; 
                                      }
                                      $filenum = $limit;
                                      $offset = ($pagenum - 1) * $filenum;
                                      $limitsel[$limit] = 'selected';
                                      $payments = payments($where, 'id', "$offset, $filenum");
                                      $payments_all = payments($where);

                                      if (is_array($payments_all)) {
                                        $payments_all = count($payments_all);
                                      } else $payments_all=0;
                                      
                                      $payments_type = payments_type();
                                     
                                  
                                     ?>
                             <div class="row form-group">
                                
                                <div class="col col-md-2"><?php echo _PERIOD; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                    <div class="col col-md-2 inlineblock"><?php echo _LINES ?><br />
                                     <select name="limit" class="form-control-sm form-control col col-md-8">
                                      <option value="50" <?php echo $limitsel[50] ?>>50</option>
                                      <option value="100" <?php echo $limitsel[100] ?>>100</option>
                                      <option value="200" <?php echo $limitsel[200] ?>>200</option>
                                      <option value="500" <?php echo $limitsel[500] ?>>500</option>
                                     </select>
                                    </div>
                                   </div> 
                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>  &nbsp;&nbsp; <?php echo _ALLFOUND.": ".$payments_all; ?>
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
                                 
                             </form>
                             </div>

                            <div class="card-body">
                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th width=5%>#</th>
                                            <th width=15%><?php echo _TIME; ?></th>
                                            <th><?php echo _PAYMENTTYPE; ?></th>
                                            <th><?php echo _COMMENT; ?></th>
                                            <th><?php echo _SUMMA; ?></th>
                                            <th><?php echo _OSTATOK; ?></th>
                                            <th><?php echo _STATUS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     if ($payments!=false) {
                                      foreach ($payments as $key => $value) {
                                        if ($value['status']==1) $status = '<span class="green">'._PAYMENTSTATUS.'</span>'; elseif ($value['status']==2) $status = '<span class="red">'._PAYMENTSTATUS3.'</span>'; else $status = '<span class="orange">'._PAYMENTSTATUS2.'</span>'; 
                                        
                                        if ($value['payment_type']!=0) $payment_type = $payments_type[$value['payment_type']]['title']; else $payment_type = '-';
                                        if ($value['payment_type']=='999') $payment_type = "PROMOCODE";
                                      	if ($value['score']) $score = "(".$value['score'].")"; else $score='';
                                        if ($value['comment']) $comment = $value['comment']; else  $comment = '-'; 
                                        $value['summa'] = moneyformat($value['summa']);
                                        $value['ostatok'] = moneyformat($value['ostatok']);
                                           echo "<tr>
                                        <td>".$key."</td>
                                        <td>".$value['create_time']."</td>
                                            <td>".$payment_type." ".$score."</td>
                                            <td>".$comment."</td>
                                            <td>".$value['summa']."$</td>
                                            <td>".$value['ostatok']."$</td>
                                            <td>".$status."</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                                $numpages = ceil($payments_all / $filenum);
                                 num_page($payments_all, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->