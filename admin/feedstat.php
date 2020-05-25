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
if ($check_login['root']!=1) exit;
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
                             $fid = intval($_GET['fid']);
                             if (!$fid) $fid = '';

                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                             if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }
                                     if ($fid) {
                                     $where .= "AND feed_id='$fid' ";
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
                                      }
                                     $stat = feed_stat($where, 'requests');
                                  
                                     $allsended = $stat['ALL']['sended'];
                                     
                                      if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }

                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-3"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _FEED ?> <input type="text" name="fid" value="<?php echo $fid ?>"  class="form-control form-control-sm"></div>
                              </div>
                                <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="feedstat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
                            <div class="card-body">

                           <?php
                           $feeds = feeds();
                           if (!empty($feeds)) {
                          // echo chart_bar("barcode", $data);
                           }
                            ?>

                          <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo _FEED ?></th>
                                            <th><?php echo _REQUEST ?></th>
                                            <th><?php echo _AVG; echo " "._TIME; echo ", "._SEK;  ?></th>
                                            <th><?php echo _EMPTY ?></th>
                                            <th>% <?php echo _EMPTY ?></th>
                                            <th><?php echo _SENDED ?></th>
                                            <th>% <?php echo _SENDED ?></th>
                                            <th><?php echo _CLICKS ?></th>
                                            <th>CTR</th>
                                            <th>CPC</th>
                                            <th>CPM</th>
                                            <th><?php echo _MONEY ?></th>
                                            <th>wm</th>
                                            <th>profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($feeds!=false) {
                                      foreach ($feeds as $id => $rows) {
                                        if ($fid && $fid!=$id) continue;
                                        if (!$stat[$id]['requests']) $stat[$id]['requests']=0;

                                      	if ($stat[$id]['empty']>0) $empty_proc = round(($stat[$id]['empty'] / $stat[$id]['requests']) * 100, 0); else {$empty_proc=0; $stat[$id]['empty']=0; }
                                      	if ($stat[$id]['sended']>0) $sended_proc = round(($stat[$id]['sended'] / $allsended) * 100, 0); else {$sended_proc=0; $stat[$id]['sended']=0; }
                                      	if ($stat[$id]['money']>0 && $stat[$id]['sended']>0) $cpm = round(($stat[$id]['money']/$stat[$id]['sended'])*1000,2); else {$cpm=0;}
                                        if ($stat[$id]['clicks']>0) $ctr = round(($stat[$id]['clicks'] / $stat[$id]['sended']) * 100, 2); else {$ctr=0; $stat[$id]['clicks']=0; }
                                        if ($stat[$id]['money']>0 && $stat[$id]['clicks']>0) $clickprice = round($stat[$id]['money']/$stat[$id]['clicks'],3); else {$clickprice=0; }
                                        if ($stat[$id]['alltime']>0) $avg_time = round($stat[$id]['alltime']/$stat[$id]['requests'],3); else {$avg_time=0; }

                                        $title = $rows['name'];
                                        $clickprice = moneyformat($clickprice);
                                        $cpm = moneyformat($cpm);
                                        
                                        
                                        if ($stat[$id]['wm_money'] > 0) {
                                            $profit = round($stat[$id]['money'] - $stat[$id]['wm_money'], 3);
                                        } else $profit = $stat[$id]['money'];
                                        
                                        $stat[$id]['wm_money'] = moneyformat($stat[$id]['wm_money']);
                                        $profit = moneyformat($profit);
                                        $stat[$id]['money'] = moneyformat($stat[$id]['money']);

                                      	 echo "<tr>
                                            <td>№".$id." ".$title."</td>
                                            <td>".$stat[$id]['requests']."</td>
                                            <td>".$avg_time."</td>
                                            <td>".$stat[$id]['empty']."</td>
                                            <td>".$empty_proc."%</td>
                                            <td>".$stat[$id]['sended']."</td>
                                            <td>".$sended_proc."%</td>
                                            <td>".$stat[$id]['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$clickprice."$</td>
                                            <td>".$cpm."$</td>
                                            <td>".$stat[$id]['money']."$</td>
                                            <td>".$stat[$id]['wm_money']."$</td>
                                            <td>".$profit."$</td>
                                        </tr>";

                                      }

                                      if ($stat['ALL']['empty']>0) $empty_proc = round(($stat['ALL']['empty'] / $stat['ALL']['requests']) * 100, 0); else {$empty_proc=0; $stat['ALL']['empty']=0; }
                                      if ($stat['ALL']['sended']>0) $sended_proc = round(($stat['ALL']['sended'] / $allsended) * 100, 2); else {$sended_proc=0; $stat['ALL']['sended']=0; }
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['sended']>0) $cpm = round(($stat['ALL']['money']/$stat['ALL']['sended'])*1000,2); else {$cpm=0; }
                                      if ($stat['ALL']['clicks']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['sended']) * 100, 2); else {$ctr=0; $stat['ALL']['clicks']=0; }
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['clicks']>0) $clickprice = round($stat['ALL']['money']/$stat['ALL']['clicks'],3); else {$clickprice=0; }
                                      if ($stat['ALL']['alltime']>0) $avg_time = round($stat['ALL']['alltime']/$stat['ALL']['requests'],3); else {$avg_time=0; }
                                     }
                                     $clickprice = moneyformat($clickprice);
                                     
                                        $cpm = moneyformat($cpm);
                                        
                                        if ($stat['ALL']['wm_money'] > 0) {
                                            $profit = round($stat['ALL']['money'] - $stat['ALL']['wm_money'], 3);
                                        } else $profit = $stat['ALL']['money'];
                                        
                                        $profit = moneyformat($profit);
                                        $stat['ALL']['wm_money'] = moneyformat($stat['ALL']['wm_money']);
                                        $stat['ALL']['money'] = moneyformat($stat['ALL']['money']);
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?></th>
                                            <th><?php echo $avg_time ?></th>
                                            <th><?php echo isset($stat['ALL']['empty']) ? $stat['ALL']['empty'] : 0; ?></th>
                                            <th><?php echo $empty_proc ?>%</th>
                                            <th><?php echo isset($stat['ALL']['sended']) ? $stat['ALL']['sended'] : 0; ?></th>
                                            <th><?php echo $sended_proc; ?>%</th>
                                            <th><?php echo isset($stat['ALL']['clicks']) ? $stat['ALL']['clicks'] : 0; ?></th>
                                            <th><?php echo $ctr; ?>%</th>
                                            <th><?php echo $clickprice; ?>$</th>
                                            <th><?php echo $cpm; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['money']) ? $stat['ALL']['money'] : 0; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['wm_money']) ? $stat['ALL']['wm_money'] : 0; ?>$</th>
                                            <th><?php echo $profit; ?>$</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                     </div>
<script>
$('#basic-datatables').DataTable();
</script>
    <br />
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->