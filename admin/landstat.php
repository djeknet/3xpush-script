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
                                     $where .= "AND sid = '".$sid."' ";
                                     }
                                     if ($subid) {
                                     $where .= "AND subid = '".$subid."' ";
                                     }
                               if ($check_login['root']==1) {
                             $only_my = intval($_GET['only_my']);
                             $admin_id = text_filter($_GET['admin_id']);
                             } else {
                              $only_my=1;  
                             }
                                    if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     }
                                      if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      $sites=sites("AND admin_id=".$check_login['getid']."");
                                      } else {
                                      $sites=sites();
                                      }
                                      
                                     $stat = landstat($where);
                                     $allviews = $stat['ALL']['views'];
                                     
                                     
                                        if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      }
                                      
                                      $landings = landings();
                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-2"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-2 inlineblock"><?php echo _SITE ?><br />
                                     <select name="sid" class="form-control-sm form-control col col-md-12">
                                     <option value="0"><?php echo _EVERY ?></option>
                                     <?php
                                     if (is_array($sites)) {
                                       foreach ($sites as $ssid => $value) {
                                        if ($sid && $sid==$ssid) $ch='selected'; else $ch='';
                                        echo "<option value=\"".$ssid."\" ".$ch.">#".$ssid." ".$value['title']."</option>";
                                       }
                                        }

                                     ?>

                                     </select></div>
                               <div class="col col-md-2">Subid <input type="text" name="subid" value="<?php echo $subid ?>"  class="form-control form-control-sm"></div>      
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

                           <?php

                            ?>

                          <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _LANDING ?>"><?php echo _LANDING ?></th>
                                            <th title="<?php echo _LANDVIEWS ?>"><?php echo _VIEWS ?></th>
                                            <th title="<?php echo _VIEWSPROCFROMALL ?>">< %</th>
                                            <th title="<?php echo _REQUEST ?>"><?php echo _REQUEST ?></th>
                                            <th title="<?php echo _FAILURE ?>"><?php echo _FAILURE ?></th>
                                            <th title="<?php echo _BLOCKEDPROC ?>">< %</th>
                                            <th title="<?php echo _SUBSCRIBERS ?>"><?php echo _SUBS ?></th>
                                            <th>CR</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                      if ($stat!=false) {
                                      foreach ($stat as $key => $value) {
                                    
                                      	if ($key!="ALL") {
                                      	 
                                        $land_arr =  $landings[$key];
                                      	if ($value['subs']>0 && $value['requests']>0) $cr = round(($value['subs'] / $value['requests']) * 100, 1); else $cr=0;
                                        if ($value['views']>0) $views_proc = round(($value['views'] / $allviews) * 100, 0); else $views_proc=0;
                                        if ($value['requests']>0 && $value['blocked_requests']>0) $blocked_proc = round(($value['blocked_requests'] / $value['requests']) * 100, 1); else $blocked_proc=0;

                                           echo "<tr>
                                            <td>#".$key." [".$land_arr['category']."]<br>  <a href=\"https://" . $settings['domain_link'] . "/land.php?test=1&lid=".$key."&subid=&tag=&price=0\" target=\"_blank\"><img src=".$land_arr['preview']." width=150></a></td>
                                            <td>".$value['views']."</td>
                                            <td>".$views_proc."%</td>
                                            <td>".$value['requests']."</td>
                                            <td>".$value['blocked_requests']."</td>
                                            <td>".$blocked_proc."%</td>
                                            <td>".$value['subs']."</td>
                                            <td>".$cr."%</td>
                                        </tr>";
                                        }

                                      }

                                      if ($stat['ALL']['subs']>0 && $stat['ALL']['requests']>0) $cr = round(($stat['ALL']['subs'] / $stat['ALL']['requests']) * 100, 1); else $cr=0;
                                      if ($stat['ALL']['views']>0) $views_proc = round(($stat['ALL']['views'] / $allviews) * 100, 0); else $views_proc=0;
                                      if ($stat['ALL']['requests']>0 && $stat['ALL']['blocked_requests']>0) $blocked_proc = round(($stat['ALL']['blocked_requests'] / $stat['ALL']['requests']) * 100, 1); else $blocked_proc=0;

                                     }

                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['views']) ? $stat['ALL']['views'] : 0; ?></th>
                                            <th><?php echo $views_proc; ?>%</th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['blocked_requests']) ? $stat['ALL']['blocked_requests'] : 0; ?></th>
                                            <th><?php echo $blocked_proc; ?>%</th>
                                            <th><?php echo isset($stat['ALL']['subs']) ? $stat['ALL']['subs'] : 0; ?></th>
                                            <th><?php echo $cr; ?>%</th>
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