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
                             $sid = intval($_GET['sid']);
                             $sid2 = intval($_GET['sid2']);
                             if ($sid2) $sid = $sid2;
                             $subid = text_filter($_GET['subid']);

                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                              if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }
                                     if ($sid) {
                                     $where .= "AND sid='$sid' ";
                                     }
                                     if ($subid) {
                                     $where .= "AND subid='$subid' ";
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
                                       $sites = sites("AND admin_id=".$check_login['getid']."");
                                      } else {
                                        $sites = sites();
                                      }
                                      
                                     $stat = day_stat($where);

                                      if (is_array($stat)) {
                                      $stat = array_reverse($stat);
                                     foreach ($stat as $date => $value) {
                                     if ($date!='ALL') {
                                     $data['title'][] = $date;
                                     $data['data'][1][] = $value['requests'];
                                     $data['data'][2][] = $value['subscribers'];
                                     $data['data'][3][] = $value['money'];
                                     }
                                     } }
                                     
                                      if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }
                                      ?>
                             <div class="row form-group">
                                 <div class="col col-md-3"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _SITE ?> <select name="sid" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                        <?php
                                        foreach ($sites as $sid1 => $arr) {
                                        if ($sid && $sid==$sid1) $sel ='selected'; else $sel='';
                                        echo "<option value=\"$sid1\" ".$sel.">#".$sid1." ".$arr['title']."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                                 <div class="col col-md-3">subid <input type="text" name="subid" value="<?php echo $subid ?>" class="form-control form-control-sm"></div>

                              </div>
                                <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="daystat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
                            <div class="card-body">
<div style="width: 1160px;">
                             <?php echo chart_line("chart_line", $data); ?><br />
</div>
<div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _DATE ?>"><?php echo _DATE ?></th>
                                            <th title="<?php echo _REQUEST ?>"><?php echo _REQUEST ?></th>
                                            <th title="<?php echo _FAILURE ?>"><?php echo _FAILURE ?></th>
                                            <th title="<?php echo _SUBSCRIBERS ?>"><?php echo _SUBSCRIBERS ?></th>
                                            <th title="<?php echo _CRTITLE ?>">CR</th>
                                            <th title="<?php echo _SENDED ?>"><?php echo _SENDED ?></th>
                                            <th title="<?php echo _UNSUBSCRIBERS ?>"><?php echo _UNSUBSCRIBERS ?></th>
                                            <th title="<?php echo _VIEWS ?>"><?php echo _VIEWS ?></th>
                                            <th title="<?php echo _SENDEDBYUSER2 ?>"><?php echo _SENDEDBYUSER ?></th>
                                            <th title="<?php echo _CLICKS ?>"><?php echo _CLICKS ?></th>
                                            <th title="CTR">CTR</th>
                                            <th title="<?php echo _REGSTAT_CPM ?>">CPM</th>
                                            <th title="<?php echo _CLICKPRICE_TITLE ?>">CPC</th>
                                            <th title="<?php echo _MONEY ?>"><?php echo _MONEY ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($stat!=false) {
                                      foreach ($stat as $key => $value) {
                                      	if ($key!='ALL') {
                                      	if ($value['subscribers']>0 && $value['requests']>0) $cr1 = round(($value['subscribers'] / $value['requests']) * 100, 1); else $cr1=0;
                                      	if ($value['uniq_sended']>0 && $value['sended']>0) $usersended = round($value['sended'] / $value['uniq_sended'], 1); else $usersended=0;
                                      	if ($value['clicks']>0 && $value['img_views']>0) $ctr = round(($value['clicks'] / $value['img_views']) * 100, 2); else $ctr=0;
                                      	if ($value['money']>0 && $value['img_views']>0) $cpm = round(($value['money']/$value['img_views'])*1000,2); else $cpm=0;
                                      	if ($value['money']>0) $clickprice = round($value['money'] / $value['clicks'], 3); else $clickprice=0;
                                      	if ($value['img_views']>0 && $value['sended']>0) $views_proc = round(($value['img_views'] / $value['sended'])*100, 0); else $views_proc=0;
                                        if ($value['blocked_requests']>0 && $value['requests']>0) $blocked_proc = round(($value['blocked_requests'] / $value['requests'])*100, 0); else $blocked_proc=0;
                                        if ($value['sended']>0 && $value['unsubs']>0) $unsubs_proc = round(($value['unsubs'] / $value['sended'])*100, 0); else $unsubs_proc=0;


                                        $value['money'] = moneyformat($value['money']);
                                        $cpm = moneyformat($cpm);
                                        $clickprice = moneyformat($clickprice);
                                        
                                      	 echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['requests']."</td>
                                            <td>".$value['blocked_requests']." <span class=table_proc>".$blocked_proc."%</span></td>
                                            <td>".$value['subscribers']."</td>
                                            <td>".$cr1."</td>
                                            <td>".$value['sended']."</td>
                                            <td>".$value['unsubs']." <span class=table_proc title=\""._UNSUBSPROC."\">".$unsubs_proc."%</span></td>
                                            <td>".$value['img_views']." <span class=table_proc title=\""._IMGVIEWPROC."\">".$views_proc."%</span></td>
                                            <td>".$usersended."</td>
                                            <td>".$value['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$cpm."$</td>
                                            <td>".$clickprice."$</td>
                                            <td>".$value['money']."$</td>
                                        </tr>";
                                        }
                                      }

                                      if ($stat['ALL']['subscribers']>0) $cr1 = round(($stat['ALL']['subscribers'] / $stat['ALL']['requests']) * 100, 1); else $cr1=0;
                                      if ($stat['ALL']['uniq_sended']>0) $usersended = round(($stat['ALL']['sended'] / $stat['ALL']['uniq_sended']) * 100, 1); else $usersended=0;
                                      if ($stat['ALL']['clicks']>0 && $stat['ALL']['sended']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['sended']) * 100, 2); else $ctr=0;
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['img_views']>0) $cpm = round(($stat['ALL']['money']/$stat['ALL']['img_views'])*1000,2); else $cpm=0;
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['clicks']>0) $clickprice = round($stat['ALL']['money'] / $stat['ALL']['clicks'], 3); else $clickprice=0;
                                      if ($stat['ALL']['img_views']>0 && $stat['ALL']['sended']>0) $views_proc = round(($stat['ALL']['img_views'] / $stat['ALL']['sended'])*100, 0); else $views_proc=0;
                                      if ($stat['ALL']['blocked_requests']>0 && $stat['ALL']['requests']>0) $blocked_proc = round(($stat['ALL']['blocked_requests'] / $stat['ALL']['requests'])*100, 0); else $blocked_proc=0;
                                      if ($stat['ALL']['unsubs']>0 && $stat['ALL']['sended']>0) $unsubs_proc = round(($stat['ALL']['unsubs'] / $stat['ALL']['sended'])*100, 0); else $unsubs_proc=0;

                                     }
                                     $stat['ALL']['money'] = moneyformat($stat['ALL']['money']);
                                     $cpm = moneyformat($cpm);
                                     $clickprice = moneyformat($clickprice);
                                        
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['blocked_requests']) ? $stat['ALL']['blocked_requests'] : 0; ?> <span class=table_proc title="<?php echo _BLOCKEDPROC; ?>"><?php echo $blocked_proc; ?>%</span></th>
                                            <th><?php echo isset($stat['ALL']['subscribers']) ? $stat['ALL']['subscribers'] : 0; ?></th>
                                            <th><?php echo $cr1; ?></th>
                                            <th><?php echo isset($stat['ALL']['sended']) ? $stat['ALL']['sended'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['unsubs']) ? $stat['ALL']['unsubs'] : 0; ?> <span class=table_proc title="<?php echo _UNSUBSPROC; ?>"><?php echo $unsubs_proc; ?>%</span></th>
                                            <th><?php echo isset($stat['ALL']['img_views']) ? $stat['ALL']['img_views'] : 0; ?> <span class=table_proc><?php echo $views_proc; ?>%</span></th>
                                            <th><?php echo $usersended; ?></th>
                                            <th><?php echo isset($stat['ALL']['clicks']) ? $stat['ALL']['clicks'] : 0; ?></th>
                                            <th><?php echo $ctr."%"; ?></th>
                                            <th><?php echo $cpm; ?>$</th>
                                            <th><?php echo $clickprice; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['money']) ? $stat['ALL']['money'] : 0; ?>$</th>
                                        </tr>
                                    </tfoot>
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