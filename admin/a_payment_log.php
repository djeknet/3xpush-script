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

                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php


                             $admin_id = intval($_GET['admin_id']);
                             if ($admin_id==0) $admin_id ='';
                             $start_date = text_filter($_GET['start_date']);
                             $end_date = text_filter($_GET['end_date']);
                             $pagenum  = intval($_GET['page']);
                             $limit  = intval($_GET['limit']);
                             if (!$pagenum) $pagenum = 1;
                             $id = intval($_GET['id']);
                             $status  = intval($_GET['status']);
                             $payment_type  = intval($_GET['payment_type']);
                             $comment = text_filter($_GET['comment']);
                             $sys_info = text_filter($_GET['sys_info']);
                             $sum_from = text_filter($_GET['sum_from']);
                             $sum_to = text_filter($_GET['sum_to']);     
                                     
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
                                     if ($admin_id) {
                                     $where .= "AND admin_id = '$admin_id'  ";
                                     $dopurl .= "&admin_id=".$admin_id."";
                                     }
                                     if ($payment_type) {
                                     $where .= "AND payment_type = '$payment_type'  ";
                                     $dopurl .= "&payment_type=".$payment_type."";
                                     }
                                     if ($comment) {
                                     $where .= "AND comment LIKE '%$comment%'  ";
                                     $dopurl .= "&comment=".$comment."";
                                     }
                                     if ($sys_info) {
                                     $where .= "AND sys_info LIKE '%$sys_info%'  ";
                                     $dopurl .= "&sys_info=".$sys_info."";
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
                                     
                                     if ($id) {
                                      $where .= "AND id = '$id'  ";  
                                     }
                                     if ($status) {
                                        if ($status==1) {
                                         $where .= "AND status = '1'  ";     
                                        }
                                        if ($status==2) {
                                         $where .= "AND status = '0'  ";     
                                        }
                                        if ($status==3) {
                                         $where .= "AND status = '2'  ";     
                                        }
                                        $dopurl .= "&status=".$status."";
                                        $statussel[$status] = "selected";
                                     }
                                     if (!$limit) $limit = 50; else {
                                       $dopurl .= "&limit=".$limit.""; 
                                      }

                                     $filenum = $limit;
                                     $offset = ($pagenum - 1) * $filenum;
                                     
                                      $payments = payments($where, 'id', "$offset, $filenum");
                                 
                                      $all_payments = payments($where);
                                      if (is_array($all_payments)) $all_payments = count($all_payments); else $all_payments = 0;
                                      $admins = admins();
                                      $payments_type = payments_type();
                                     ?>
                              <div class="row form-group">
                                <div class="col col-md-2">ID <input type="text" name="id" value="<?php echo $id ?>" class="form-control form-control-sm"></div>
                                <div class="col col-md-2"><?php echo _PERIOD; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">user id <input type="text" name="admin_id" value="<?php echo $admin_id ?>" class="form-control form-control-sm"></div>
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
                                    <div class="col col-md-2"><?php echo _STATUS ?> <select name="status" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                  <option value="1" <?php echo $statussel[1]; ?>><?php echo _PAYMENTSTATUS; ?></option>
                                  <option value="2" <?php echo $statussel[2]; ?>><?php echo _PAYMENTSTATUS2; ?></option>
                                  <option value="3" <?php echo $statussel[3]; ?>><?php echo _PAYMENTSTATUS3; ?></option>
                                    </select>
                                    </div>
                                    <div class="col col-md-2"><?php echo _COMMENT ?> <input type="text" name="comment" value="<?php echo $comment ?>" class="form-control form-control-sm"></div>
                                    <div class="col col-md-2">Sys info <input type="text" name="sys_info" value="<?php echo $sys_info ?>" class="form-control form-control-sm"></div>
                                    <div class="col col-md-2 inlineblock"><?php echo _SUMMA ?><br />
                                   <input type="text" name="sum_from" value="<?php echo $sum_from ?>"  placeholder="<?php echo _FROM ?>" class="form-control form-control-sm col col-md-4"> 
                                   <input type="text" name="sum_to" value="<?php echo $sum_to ?>"  placeholder="<?php echo _TILL ?>" class="form-control form-control-sm col col-md-4">
                                   </div>
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
                            <div class="card-body">
                            

                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                      <th width=5%>#</th>
                                            <th width=15%><?php echo _TIME; ?></th>
                                            <th>user</th>
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
                                        if ($value['status']==1) $status = '<span class="green">'._PAYMENTSTATUS.'</span>'; else $status = "<span class=\"orange\">"._PAYMENTSTATUS2."</span>"; 
                                    
                                        if ($value['payment_type']!=0) $payment_type = $payments_type[$value['payment_type']]['title']; else $payment_type = '-';
                                      	if ($value['score']) $score = "(".$value['score'].")"; else $score='';
                                        if ($value['comment']) $comment = $value['comment']; else  $comment = '-'; 
                                        $login = $admins[$value['admin_id']]['login'];
                                        if ($value['sys_info']) {
                                            $sys_info = "<br><font class=\"small red\">Sys info: ".$value['sys_info']."</font>";
                                        } else $sys_info='';
                                        
                                           echo "<tr>
                                        <td>".$key."</td>
                                        <td>".$value['create_time']."</td>
                                        <td><a href=?m=a_users&admin_id=".$value['admin_id'].">#".$value['admin_id']."</a> (".$login.")</td>
                                        <td>".$payment_type." ".$score."</td>
                                        <td>".$comment."".$sys_info."</td>
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
                                $numpages = ceil($all_payments / $filenum);
                                 num_page($all_payments, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

