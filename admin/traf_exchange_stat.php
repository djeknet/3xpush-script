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
                             $partner_sid = intval($_GET['partner_sid']);
                             
                             if ($sid2) $sid = $sid2;
                             $subid = text_filter($_GET['subid']);
                             
                                     if ($check_login['root']==1) {
                                       $only_my = intval($_GET['only_my']);
                                       $admin_id = text_filter($_GET['admin_id']);
                                       } else {
                                       $only_my=1;  
                                      }
                                      
                             if ($only_my==1) {       
                             $sites = sites("AND admin_id=".$check_login['getid']." AND type=1");
                             } else {
                             $sites = sites("AND type=1");   
                             }
                             $all_sites = sites();  
                             
                             if (!$sid) {
                             if (is_array($sites)) {
                                foreach ($sites as $key => $value) {
                                 $sids[] = $key;
                                }
                                $sids = implode(',', $sids);
                             }
                             } else $sids = $sid;
 
                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                              if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }
                                     if ($partner_sid) {
                                     $where .= "AND admin_site = '".$partner_sid."' ";   
                                     }
                                    
                                     $stat = traf_exchange_stat_inout($sids, $where);
                                     
                                     $traf_exchange = traf_exchange_admins("AND site_id IN (".$sids.")");  
                                        if (is_array($traf_exchange)) {
                                     foreach ($traf_exchange as $key => $value) {
                                           $url = $all_sites[$value['admin_site']]['url'];
                                           $partner_sids[$value['admin_site']] = $url;
                                          }
                                        }

                                      if (is_array($stat)) {
                                      $stat = array_reverse($stat);
                                     foreach ($stat as $date => $value) {
                                     if ($date!='ALL') {
                                     $data['legends'][] = $date;
                                     $data['title'][_TRAF_EXCH_SENDED_OUT][] = isset($value['out']['sended']) ? $value['out']['sended'] : 0;
                                     $data['title'][_TRAF_EXCH_SENDED_IN][] = isset($value['in']['sended']) ? $value['in']['sended'] : 0;
                                     $data['title'][_TRAF_EXCH_CLICKS_IN][] = isset($value['in']['clicks']) ? $value['in']['clicks'] : 0;
                                     $data['title'][_TRAF_EXCH_CLICKS_OUT][] = isset($value['out']['clicks']) ? $value['out']['clicks'] : 0;
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
                                    
                                <div class="col col-md-3"><?php echo _PARTNER_SITE ?> <select name="partner_sid" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                        <?php
                                        foreach ($partner_sids as $sid1 => $url) {
                                        if ($partner_sid && $partner_sid==$sid1) $sel ='selected'; else $sel='';
                                        echo "<option value=\"$sid1\" ".$sel.">#".$sid1." ".$url."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                              </div>
                                <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
                            <div class="card-body">
<div style="width: 1160px;">
                             <?php echo chart_line2("chart_line", $data); ?><br />
</div>
                                <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _DATE ?>"><?php echo _DATE ?></th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_1 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_1; ?>"><?php echo _TRAF_EXCH_SENDED_OUT; ?></span></th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_2 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_2; ?>"><?php echo _TRAF_EXCH_CLICKS_IN; ?></span></th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_3 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_3; ?>"><?php echo _TRAF_EXCH_SENDED_IN; ?></span></th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_4 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_4; ?>"><?php echo _TRAF_EXCH_CLICKS_OUT; ?></span></th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_5 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_5; ?>">CTR In</span> </th>
                                            <th title="<?php echo _EXCHANGE_TOOLTIP_6 ?>"><span title="<?php echo _EXCHANGE_TOOLTIP_6; ?>">CTR Out</span> </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($stat!=false) {
                                      foreach ($stat as $key => $value) {
                                      	if ($key!='ALL') {

                                      	if ($value['in']['sended']>0 && $value['out']['clicks']>0) $ctr_out = round(($value['out']['clicks'] / $value['in']['sended']) * 100, 2); else $ctr_out=0;
                                      	if ($value['out']['sended']>0 && $value['in']['clicks']>0) $ctr_in = round(($value['in']['clicks'] / $value['out']['sended']) * 100, 2); else $ctr_in=0;
                                      	if (!$value['out']['sended']) $value['out']['sended'] = 0;
                                        if (!$value['in']['sended']) $value['in']['sended'] = 0;
                                        if (!$value['out']['clicks']) $value['out']['clicks'] = 0;
                                        if (!$value['in']['clicks']) $value['in']['clicks'] = 0;
                                        
                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['out']['sended']."</td>
                                            <td>".$value['in']['clicks']."</td>
                                            <td>".$value['in']['sended']."</td>
                                            <td>".$value['out']['clicks']."</td>
                                            <td>".$ctr_in."%</td>
                                            <td>".$ctr_out."%</td>
                                        </tr>";
                                        }
                                      }

                                     	if ($stat['ALL']['out']['sended']>0 && $stat['ALL']['in']['clicks']>0) $ctr_in = round(($stat['ALL']['in']['clicks'] / $stat['ALL']['out']['sended']) * 100, 2); else $ctr_out=0;
                                      	if ($stat['ALL']['in']['sended']>0 && $stat['ALL']['out']['clicks']>0) $ctr_out = round(($stat['ALL']['out']['clicks'] / $stat['ALL']['in']['sended']) * 100, 2); else $ctr_in=0;
                                      	
                                     }
                                        
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['out']['sended']) ? $stat['ALL']['out']['sended'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['in']['clicks']) ? $stat['ALL']['in']['clicks'] : 0; ?> </th>
                                            <th><?php echo isset($stat['ALL']['in']['sended']) ? $stat['ALL']['in']['sended'] : 0; ?> </th>
                                            <th><?php echo isset($stat['ALL']['out']['clicks']) ? $stat['ALL']['out']['clicks'] : 0; ?></th>
                                            <th><?php echo $ctr_in."%"; ?></th>
                                            <th><?php echo $ctr_out."%"; ?></th>
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