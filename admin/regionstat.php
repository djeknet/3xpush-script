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
                             $regions = $_GET['regions'];
                             $isolist = isolist();

                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                             if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }
                                     if ($regions) {
                                     $regions2 = text_filter(implode(',', $regions));
                                     $regions2 = str_replace(",", "','" , $regions2);
                                     $where .= "AND cc IN ('$regions2') ";
                                     }
                                      
                                     if ($check_login['root']==1) {
                                     $only_my = intval($_GET['only_my']);
                                     $admin_id = text_filter($_GET['admin_id']);
                                     } else {
                                      $only_my=1;  
                                      }
                                       if ($sid) {
                                     $where .= "AND sid='$sid' ";
                                     }
                                     if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     }
                                       if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                       $sites = sites("AND admin_id=".$check_login['getid']."");
                                      } else {
                                        $sites = sites();
                                      }
                                     $stat = region_stat($where, 'sended');

                                     $allsended = $stat['ALL']['sended'];
                                     $allunsubs = $stat['ALL']['unsubs'];
                                     $allsubscribers = $stat['ALL']['subscribers'];
                                     $allempty = $stat['ALL']['empty'];
                                     $allmoney = $stat['ALL']['money'];
                                       if (is_array($stat)) {
                                      foreach ($stat as $cc => $rows) {
                                      	if ($cc=='ALL') continue;
                                        if ($rows['money']) {
                                        $proc = round(($rows['money'] / $allmoney) * 100, 0);

                                        if ($proc>=1) {
                                      	$data['title'][] = $cc;
                                      	$data['data'][] = $proc;
                                      	} else {
                                      	$data['title'][999] = 'other';
                                      	$data['data'][999] += $proc;
                                      	}
                                      	}
                                      }
                                       }
                                       
                                        if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }
                                      $isolist = isolist();
                                      
                                      foreach ($isolist as $key => $value) {
                                      $region_titles[$value['iso']] = $value[$lang];
                                      }


                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-2"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                   <div class="col col-md-2"><?php echo _SITE ?> <select name="sid" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                        <?php
                                        foreach ($sites as $sid1 => $arr) {
                                        if ($sid && $sid==$sid1) $sel ='selected'; else $sel='';
                                        echo "<option value=\"$sid1\" ".$sel.">#".$sid1." ".$arr['title']."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                                   <div class="col col-md-3"><?php echo _REGIONS ?>
                                   <select data-placeholder="<?php echo _CHOSE; ?>" multiple class="select1" name="regions[]">
                                    <option value=""></option>
                                    <?php
                                    foreach ($isolist as $key => $arr) {
                                        if ($regions && in_array($arr['iso'], $regions)) $sel ='selected'; else $sel='';
                                    echo "<option value=\"".$arr['iso']."\" ".$sel.">".$arr[$lang]." [".$arr['iso']."]</option>";
                                    }
         ?>
    </select>
       </div>
                              </div>
 <script src="vendors/chosen/chosen.jquery.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery(".select1").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "300px"
        });
         });
</script>
                                <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="regionstat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
                            <div class="card-body">
                                 <div align="center"><h5><?php echo _REGIONS_TEXT1  ?></h5></div>
                                <?php echo chart_pie("pieChart", $data); ?>

                               <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _REGION ?>"><?php echo _REGION ?></th>
                                            <th title="<?php echo _REGSTAT_REQ ?>">req</th>
                                            <th title="<?php echo _REGSTAT_NEW ?>">new</th>
                                            <th title="<?php echo _REGSTAT_CR ?>">CR</th>
                                            <th title="<?php echo _REGSTAT_CR2 ?>">CR2</th>
                                            <th title="<?php echo _REGSTAT_EMPTY ?>"><?php echo _EMPTY ?></th>
                                            <th title="<?php echo _REGSTAT_EMPTY_PROC ?>">&lt; %</th>
                                            <th title="<?php echo _SENDED ?>"><?php echo _SENDED ?></th>
                                            <th title="<?php echo _REGSTAT_SENDED_PROC ?>">&lt; %</th>
                                            <th title="<?php echo _REGSTAT_UNS ?>">uns</th>
                                            <th title="<?php echo _REGSTAT_UNS_PROC ?>">&lt; %</th>
                                            <th title="<?php echo _VIEWS ?>">vs</th>
                                            <th title="<?php echo _CLICKS ?>"><?php echo _CLICKS ?></th>
                                            <th title="<?php echo _REGSTAT_CTR ?>">CTR</th>
                                            <th title="<?php echo _REGSTAT_CLICKPRICE ?>">CPC</th>
                                            <th title="<?php echo _REGSTAT_CPM ?>">CPM</th>
                                            <th title="<?php echo _MONEY ?>"><?php echo _MONEY ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($stat!=false) {
                                      foreach ($stat as $cc => $rows) {
                                         if ($cc=='ALL') continue;
                                         if ($cc=='') $cc = 'unknown';
                                         if (!$rows['requests']) $rows['requests']=0;

                                      	if ($rows['empty']>0) $empty_proc = round(($rows['empty'] / $allempty) * 100, 1); else {$empty_proc=0; $rows['empty']=0; }
                                      	if ($rows['sended']>0) $sended_proc = round(($rows['sended'] / $allsended) * 100, 1); else {$sended_proc=0; $rows['sended']=0; }
                                      	if ($rows['money']>0 && $rows['img_views']>0) $cpm = round(($rows['money']/$rows['img_views'])*1000,2); else {$cpm=0; $rows['money']=0; }
                                        if ($rows['clicks']>0 && $rows['img_views']>0) $ctr = round(($rows['clicks'] / $rows['img_views']) * 100, 2); else {$ctr=0; $rows['clicks']=0; }
                                        if ($rows['money']>0) $clickprice = round($rows['money']/$rows['clicks'],3); else {$clickprice=0; }
                                        if ($rows['unsubs']>0) $unsubs_proc = round(($rows['unsubs']/$allunsubs)*100,0); else {$unsubs_proc=0; }
                                        if ($rows['subscribers']>0 && $rows['requests']>0 && $allsubscribers) {
                                        	$subscribers_proc = round(($rows['subscribers']/$allsubscribers)*100,0);
                                            $cr = round(($rows['subscribers'] / $rows['requests']) * 100, 2); } else {$subscribers_proc=0; $cr=0; }

                                      	$clickprice = moneyformat($clickprice);
                                        $cpm = moneyformat($cpm);
                                        $rows['money'] = moneyformat($rows['money']);

                                           echo "<tr>
                                            <td>".$cc." <span class=table_proc>".$region_titles[$cc]."</span></td>
                                            <td>".$rows['requests']."</td>
                                            <td>".$rows['subscribers']."</td>
                                            <td>".$cr."%</td>
                                            <td>".$subscribers_proc."%</td>
                                            <td>".$rows['empty']."</td>
                                            <td>".$empty_proc."%</td>
                                            <td>".$rows['sended']."</td>
                                            <td>".$sended_proc."%</td>
                                            <td>".$rows['unsubs']."</td>
                                            <td>".$unsubs_proc."%</td>
                                            <td>".$rows['img_views']."</td>
                                            <td>".$rows['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$clickprice."$</td>
                                            <td>".$cpm."$</td>
                                            <td>".$rows['money']."$</td>
                                        </tr>";

                                      }

                                      if ($stat['ALL']['money']>0 && $stat['ALL']['img_views']>0) $cpm = round(($stat['ALL']['money']/$stat['ALL']['img_views'])*1000,2); else {$cpm=0; $stat['ALL']['money']=0; }
                                      if ($stat['ALL']['clicks']>0 && $stat['ALL']['img_views']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['img_views']) * 100, 2); else {$ctr=0; $stat['ALL']['clicks']=0; }
                                      if ($stat['ALL']['money']>0) $clickprice = round($stat['ALL']['money']/$stat['ALL']['clicks'],3); else {$clickprice=0; }
                                      if ($stat['ALL']['subscribers']>0) {$cr = round(($stat['ALL']['subscribers']/$stat['ALL']['requests'])*100,2); }else {$cr=0; }
                                     }
                                     $clickprice = moneyformat($clickprice);
                                        $cpm = moneyformat($cpm);
                                        $stat['ALL']['money'] = moneyformat($stat['ALL']['money']);
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['subscribers']) ? $stat['ALL']['subscribers'] : 0; ?></th>
                                            <th><?php echo $cr ?>%</th>
                                            <th>-</th>
                                            <th><?php echo isset($stat['ALL']['empty']) ? $stat['ALL']['empty'] : 0; ?></th>
                                            <th>-</th>
                                            <th><?php echo isset($stat['ALL']['sended']) ? $stat['ALL']['sended'] : 0; ?></th>
                                            <th>-</th>
                                            <th><?php echo isset($stat['ALL']['unsubs']) ? $stat['ALL']['unsubs'] : 0; ?></th>
                                            <th>-</th>
                                            <th><?php echo isset($stat['ALL']['img_views']) ? $stat['ALL']['img_views'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['clicks']) ? $stat['ALL']['clicks'] : 0; ?></th>
                                            <th><?php echo $ctr; ?>%</th>
                                            <th><?php echo $clickprice; ?>$</th>
                                            <th><?php echo $cpm; ?>$</th>
                                            <th><?php echo isset($stat['ALL']['money']) ? $stat['ALL']['money'] : 0; ?>$</th>
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