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
                                      $sites=sites("AND admin_id=".$check_login['getid']."");
                                      } else {
                                      $sites=sites();
                                      }
                                     $stat = browser_stat($where, 'sended');

                                     $br_list = browsers();
                                     $allsended = $stat['ALL']['sended'];
                                     $sites=sites("AND admin_id=".$check_login['getid']."");
                                     
                                     if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                     
                                      }
                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-3"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-3 inlineblock"><?php echo _SITE ?><br />
                                     <select name="sid" class="form-control-sm form-control col col-md-6">
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
                                            <th title="<?php echo _MACROS12 ?>"><?php echo _MACROS12 ?></th>
                                            <th title="<?php echo _LANDVIEWS ?>">LV</th>
                                            <th title="<?php echo _REQUEST ?>"><?php echo _REQUEST ?></th>
                                            <th title="<?php echo _FAILURE ?>"><?php echo _FAILURE ?></th>
                                            <th title="<?php echo _SUBSCRIBERS ?>">subs</th>
                                            <th title="<?php echo _CRTITLE ?>">CR</th>
                                            <th title="<?php echo _UNSUBSCRIBERS ?>"><?php echo _UNSUBSCRIBERS ?></th>
                                            <th title="<?php echo _SENDED ?>"><?php echo _SENDED ?></th>
                                            <th title="<?php echo _VIEWS ?>">views</th>
                                            <th title="<?php echo _SENDBYUSERTITLE ?>"><?php echo _SENDEDBYUSER ?></th>
                                            <th title="<?php echo _CLICKS ?>"><?php echo _CLICKS ?></th>
                                            <th title="CTR">CTR</th>
                                            <th title="CPM">CPM</th>
                                            <th title="<?php echo _CLICKPRICETITLE ?>">CPC</th>
                                            <th title="<?php echo _MONEYSPENTTITLE ?>"><?php echo _MONEYSPENT ?></th>
                                            <th title="<?php echo _MONEY ?>"><?php echo _MONEY ?></th>
                                            <th title="<?php echo _PROFIT ?>"><?php echo _PROFIT ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                      if ($stat!=false) {
                                      foreach ($stat as $brid => $value) {
                                      	if ($brid!='ALL') {
                                      	if ($value['subscribers']>0 && $value['requests']>0) $cr1 = round(($value['subscribers'] / $value['requests']) * 100, 1); else $cr1=0;
                                      	if ($value['uniq_sended']>0 && $value['sended']>0) $usersended = round($value['sended'] / $value['uniq_sended'], 1); else $usersended=0;
                                      	if ($value['clicks']>0 && $value['img_views']>0) $ctr = round(($value['clicks'] / $value['img_views']) * 100, 2); else $ctr=0;
                                      	if ($value['money']>0) $cpm = round(($value['money']/$value['sended'])*1000,2); else $cpm=0;
                                      	if ($value['money']>0) $clickprice = round($value['money'] / $value['clicks'], 3); else $clickprice=0;
                                      	if ($value['img_views']>0 && $value['sended']>0) $views_proc = round(($value['img_views'] / $value['sended'])*100, 0); else $views_proc=0;
                                        if ($value['traf_cost']>0) $profit = round($value['money'] - $value['traf_cost'], 3); else $profit = $value['money'];
                                        if ($value['land_views']>0 && $value['requests']>0) $requests_proc = round(($value['requests'] / $value['land_views'])*100, 0); else $requests_proc=0;

                                        if (!$brid) {$bricon = "unknown"; $brname = "unknown";} else {$bricon = $br_list[$brid]['key']; $brname = $br_list[$brid]['name'];}
                                        
                                        $value['money'] = moneyformat($value['money']);
                                        $value['traf_cost'] = moneyformat($value['traf_cost']);
                                        $cpm = moneyformat($cpm);
                                        $clickprice = moneyformat($clickprice);
                                        $profit = moneyformat($profit);
                                        
                                        $icon = "images/browser/" . $bricon . ".png";
                                        if (!file_exists($icon)) {
                                            $icon = "images/browser/unknown.png";
                                            }
                                        
                                      	 echo "<tr>
                                            <td><img src=\"".$icon."\" border=0 align=absmiddle width=16> ".$brname."</td>
                                            <td>".$value['land_views']."</td>
                                            <td>".$value['requests']." <span class=table_proc title=\""._REQUESTPROC."\">".$requests_proc."%</span></td>
                                            <td>".$value['blocked_requests']."</td>
                                            <td>".$value['subscribers']."</td>
                                            <td>".$cr1."</td>
                                            <td>".$value['unsubs']."</td>
                                            <td>".$value['sended']."</td>
                                            <td>".$value['img_views']." <span class=table_proc title=\""._VIEWSPROC."\">".$views_proc."%</span></td>
                                            <td>".$usersended."</td>
                                            <td>".$value['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$cpm."$</td>
                                            <td>".$clickprice."$</td>
                                            <td>".$value['traf_cost']."$</td>
                                            <td>".$value['money']."$</td>
                                            <td>".$profit."$</td>
                                        </tr>";
                                        }

                                      }

                                      if ($stat['ALL']['subscribers']>0) $cr1 = round(($stat['ALL']['subscribers'] / $stat['ALL']['requests']) * 100, 1); else $cr1=0;
                                      if ($stat['ALL']['uniq_sended']>0) $usersended = round(($stat['ALL']['sended'] / $stat['ALL']['uniq_sended']) * 100, 1); else $usersended=0;
                                      if ($stat['ALL']['clicks']>0 && $stat['ALL']['img_views']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['img_views']) * 100, 2); else $ctr=0;
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['sended']>0) $cpm = round(($stat['ALL']['money']/$stat['ALL']['sended'])*1000,2); else $cpm=0;
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['clicks']>0) $clickprice = round($stat['ALL']['money'] / $stat['ALL']['clicks'], 3); else $clickprice=0;
                                      if ($stat['ALL']['img_views']>0 && $stat['ALL']['sended']>0) $views_proc = round(($stat['ALL']['img_views'] / $stat['ALL']['sended'])*100, 0); else $views_proc=0;
                                      if ($stat['ALL']['traf_cost']>0) $profit = round($stat['ALL']['money'] - $stat['ALL']['traf_cost'], 3); else $profit=$stat['ALL']['money'];
                                      if ($stat['ALL']['land_views']>0 && $stat['ALL']['requests']>0) $requests_proc = round(($stat['ALL']['requests'] / $stat['ALL']['land_views'])*100, 0); else $requests_proc=0;

                                     }
                                     $stat['ALL']['money'] = moneyformat($stat['ALL']['money']);
                                     $stat['ALL']['traf_cost'] = moneyformat($stat['ALL']['traf_cost']);
                                     $cpm = moneyformat($cpm);
                                     $clickprice = moneyformat($clickprice);
                                     $profit = moneyformat($profit);
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['land_views']) ? $stat['ALL']['land_views'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?> <span class=table_proc title="<?php echo  _REQUESTPROC; ?>"><?php echo $requests_proc; ?>%</span></th>
                                            <th><?php echo isset($stat['ALL']['blocked_requests']) ? $stat['ALL']['blocked_requests'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['subscribers']) ? $stat['ALL']['subscribers'] : 0; ?></th>
                                            <th><?php echo $cr1; ?></th>
                                            <th><?php echo isset($stat['ALL']['unsubs']) ? $stat['ALL']['unsubs'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['sended']) ? $stat['ALL']['sended'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['img_views']) ? $stat['ALL']['img_views'] : 0; ?> <span class=table_proc><?php echo $views_proc; ?>%</span></th>
                                            <th><?php echo $usersended; ?></th>
                                            <th><?php echo isset($stat['ALL']['clicks']) ? $stat['ALL']['clicks'] : 0; ?></th>
                                            <th><?php echo $ctr."%"; ?></th>
                                            <th><?php echo $cpm; ?>$</th>
                                            <th><?php echo $clickprice; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['traf_cost']) ? $stat['ALL']['traf_cost'] : 0; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['money']) ? $stat['ALL']['money'] : 0; ?>$</th>
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