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
include("navbar.php");
?>


        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">


                    <div class="col-md-12">
                        <div class="card">
                        <div class="card-body card-block">
                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $start_date = text_filter($_GET['start_date']);
                             $end_date = text_filter($_GET['end_date']);
                             $update_from = text_filter($_GET['update_from']);
                             $update_to = text_filter($_GET['update_to']);
                             $feed_id = intval($_GET['feed_id']);
                             $title = text_filter($_GET['title']);
                             $descr = text_filter($_GET['descr']);
                             $url = text_filter($_GET['url']);
                             $sended = intval($_GET['sended']);
                             $sended_type = intval($_GET['sended_type']);
                             $clicks = intval($_GET['clicks']);
                             $clicks_type = intval($_GET['clicks_type']);
                             $ctr = text_filter($_GET['ctr']);
                             $ctr_type = text_filter($_GET['ctr_type']);
                             $bid = text_filter($_GET['bid']);
                             $bid_type = text_filter($_GET['bid_type']);
                             $limit = intval($_GET['limit']);
                             if (!$limit) $limit=50;
                             $limitsel[$limit] = 'selected';

                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                                     if ($update_from && $update_to) {
                                     $where = "AND update_date >= '".$update_from."' AND update_date <= '".$update_to."' ";
                                     } elseif ($update_from) {
                                     $where = "AND update_date >= '".$update_from."' ";
                                     } elseif ($update_to) {
                                     $where = "AND update_date <= '".$update_to."' ";
                                     }
                                     if ($start_date && $end_date) {
                                     $where .= "AND create_date >= '".$start_date."' AND create_date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where .= "AND create_date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where .= "AND create_date <= '".$end_date."' ";
                                     }
                                     if ($feed_id) {
                                     if ($feed_id==99) $feed_id = 0;
                                     $where .= "AND feed_id='$feed_id' ";
                                     }
                                     if ($title) {
                                     $where .= "AND title LIKE '%$title%' ";
                                     }
                                     if ($descr) {
                                     $where .= "AND description LIKE '%$descr%' ";
                                     }
                                     if ($url) {
                                     $where .= "AND url LIKE '%$url%' ";
                                     }
                                     if ($sended) {
        	                          if ($sended_type==0) { $sended_types = ">=";}
        	                          elseif ($sended_type==1) {$sended_types = "<=";}
                                      $where .= "AND sended $sended_types '$sended'  ";
                                      $sended_typesel[$sended_type] = "selected";
                                      }
                                      if ($clicks) {
        	                          if ($clicks_type==0) { $clicks_types = ">=";}
        	                          elseif ($clicks_type==1) {$clicks_types = "<=";}
                                      $where .= "AND clicks $clicks_types '$clicks'  ";
                                      $clicks_typesel[$clicks_type] = "selected";
                                      }
                                      if ($ctr) {
        	                          if ($ctr_type==0) { $ctr_types = ">=";}
        	                          elseif ($ctr_type==1) {$ctr_types = "<=";}
                                      $where .= "AND (clicks/sended)*100 $ctr_types '$ctr'  ";
                                      $ctr_typesel[$ctr_type] = "selected";
                                      }
                                      if ($bid) {
        	                          if ($bid_type==0) { $bid_types = ">=";}
        	                          elseif ($bid_type==1) {$bid_types = "<=";}
                                      $where .= "AND money/clicks $bid_types '$bid'  ";
                                      $bid_typesel[$bid_type] = "selected";
                                      }
                                     if ($check_login['root']==1) {
                                     $only_my = intval($_GET['only_my']);
                                     $admin_id = text_filter($_GET['admin_id']);
                                     } else {
                                      $only_my=1;  
                                      }
                                     if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     $dopurl .= "&admin_id=$admin_id";
                                     }
                                      if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      } else {
                                      }
                                     $stat = adv_stat($where, 'id', $limit);
                                     $all_stat = adv_stat($where, 'id');
                                     if (is_array($all_stat)) {
                                        $allfound = count($all_stat);
                                      } else $allfound=0;
                                     
                                     if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }
                                       $feeds = feeds("AND admin_id=".$check_login['getid']."");
                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-3"><?php echo _ADDED; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _UPDATE; echo " "._FROM ?> <input type="text" name="update_from" id='datepicker3' value="<?php echo $update_from ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="update_to" id='datepicker4' value="<?php echo $update_to ?>" class="form-control form-control-sm"></div>

                                 <div class="col col-md-3"><?php echo _TITLE ?> <input type="text" name="title" value="<?php echo $title ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _DESCR ?> <input type="text" name="descr" value="<?php echo $descr ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _LINK ?> <input type="text" name="url" value="<?php echo $url ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3 inlineblock"><?php echo _SENDED ?><br />
                                     <select name="sended_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $sended_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $sended_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="sended" value="<?php echo $sended ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-3 inlineblock"><?php echo _CLICKS ?><br />
                                     <select name="clicks_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $clicks_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $clicks_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="clicks" value="<?php echo $clicks ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-3 inlineblock">CTR<br />
                                     <select name="ctr_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $ctr_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $ctr_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="ctr" value="<?php echo $ctr ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-3 inlineblock">BID<br />
                                     <select name="bid_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $bid_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $bid_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="bid" value="<?php echo $bid ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-2 inlineblock"><?php echo _LINES ?><br />
                                     <select name="limit" class="form-control-sm form-control col col-md-8">
                                      <option value="50" <?php echo $limitsel[50] ?>>50</option>
                                      <option value="100" <?php echo $limitsel[100] ?>>100</option>
                                      <option value="150" <?php echo $limitsel[150] ?>>150</option>
                                      <option value="200" <?php echo $limitsel[200] ?>>200</option>
                                     </select>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                 $('#datepicker').flatpickr({
                                     enableTime: true,
                                     dateFormat: "Y-m-d H:i",
                                     time_24hr: true,
                                 });
                                  $('#datepicker2').flatpickr({
                                      enableTime: true,
                                      dateFormat: "Y-m-d H:i",
                                      time_24hr: true,
                                 });
                                   $('#datepicker3').flatpickr({
                                       enableTime: true,
                                       dateFormat: "Y-m-d H:i",
                                       time_24hr: true,
                                 });
                                   $('#datepicker4').flatpickr({
                                       enableTime: true,
                                       dateFormat: "Y-m-d H:i",
                                       time_24hr: true,
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="advstat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp;&nbsp;      <?php echo _ALLFOUND.": <strong>".$allfound."</strong>"; ?> <br />
                             </form>
                             </div>
                            <div class="card-body">
                                <script type="text/javascript">
                                  function viewblock(id, context) {

                                      if($('#'+id).css('display')=='none') {
                                          $('#'+id).show();

                                          $(context).html('<i class="fa fa-picture-o"></i> <?php echo _HIDEIMG ?>');
                                      }
                                      else {
                                          $('#'+id).hide();
                                          $(context).html('<i class="fa fa-picture-o"></i> <?php echo _SHOWIMG ?>');
                                      }
                                }
                                </script>
                                <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _ADDED ?> / <?php echo _UPDATE ?>"><?php echo _ADDED ?> / <?php echo _UPDATE ?></th>
                                            <th title="<?php echo _ADV ?>" style="width: 25%"><?php echo _ADV ?></th>
                                            <th title="<?php echo _UNIQSENDED ?>"><?php echo _UNIQSENDED ?></th>
                                            <th title="<?php echo _SENDED ?>"><?php echo _SENDED ?></th>
                                            <th title="<?php echo _CLICKS ?>"><?php echo _CLICKS ?></th>
                                            <th title="CTR">CTR</th>
                                            <th title="BID">BID</th>
                                            <th title="<?php echo _MONEY ?>"><?php echo _MONEY ?></th>
                                            <th title="CPM">CPM</th>
                                            <th title="<?php echo _UNSUBSCRIBERS ?>"><?php echo _UNSUBSCRIBERS ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($stat!=false) {
                                        $feeds = feeds("AND admin_id=".$check_login['getid']."");
                                        $num=1;
                                      foreach ($stat as $key => $rows) {
                                         $feed_name = $feeds[$rows['feed_id']]['name'];
                                         if (!$feed_name) $feed_name = 'my ads'; elseif ($check_login['root']==1) $feed_name = 'feed '.$feed_name; else $feed_name = '';
                                         

                                      	if ($rows['clicks']>0) $ctr = round(($rows['clicks'] / $rows['sended']) * 100, 2); else $ctr=0;
                                      	if ($rows['money']>0 && $rows['sended']>0) $cpm = round(($rows['money']/$rows['sended'])*1000,2); else $cpm=0;
                                        if ($rows['unsubs']>0 && $rows['uniq_sended']>0) $unsubs_proc = round(($rows['unsubs']/$rows['uniq_sended'])*100,2); else $unsubs_proc=0;
                                        if ($rows['uniq_sended']>0 && $rows['sended']>0) $uniq_sended = round($rows['sended']/$rows['uniq_sended'],1); else $uniq_sended=0;

                                        if ($rows['blocked']==1) {
                                         $blocked = "<b class=red>"._BLOCKED." ".$rows['last_check']."</b><br />";
                                         } else $blocked='';

                                        if ($rows['clicks'] && $rows['money']) {
                                        $click_price = round($rows['money'] / $rows['clicks'], 2);
                                        } else  $click_price=0;
                                        if ($rows['image']) {
                                         $image = "<a href=\"#\" onclick=\"viewblock('showimg_".$num."', this); return false;\" class=small><i class=\"fa fa-picture-o\"></i> "._SHOWIMG."</a><div id=\"showimg_".$num."\" style='display: none;'><img src=".$rows['image']." class=bigimg></div><br>";
                                        } else $image='';
                                        
                                        $rows['money'] = moneyformat($rows['money']);
                                        $click_price = moneyformat($click_price);
                                        $cpm = moneyformat($cpm);
                                        
                                      	 echo "<tr>
                                            <td>".$rows['create_date']."<br /> ".$rows['update_date']."</td>
                                            <td>".$blocked."<font class=small>".$feed_name."</font><br /><img src=".$rows['icon']." width=50 align=left class=advimg> <a href=".$rows['url']." target=\"_blank\"><b>".$rows['title']."</b></a><br />".$rows['description']."<br /> ".$image." <font class=small>#".$rows['hash']."</font></td>
                                            <td>".$rows['uniq_sended']."</td>
                                            <td>".$rows['sended']." <span class=table_proc>".$uniq_sended."</span></td>
                                            <td>".$rows['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$click_price."$</td>
                                            <td>".$rows['money']."$</td>
                                            <td>".$cpm."$</td>
                                            <td>".$rows['unsubs']."  <span class=table_proc>".$unsubs_proc."%</span></td>
                                        </tr>";
                                      $num++;
                                      }

                                     }
                                    ?>
                                    </tbody>
                                </table>
     </div>
<script>
$('#basic-datatables').DataTable();
</script>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
