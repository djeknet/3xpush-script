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

                                     if ($subid) {
                                     $where .= "AND subid='$subid' ";
                                     }
                                     if ($sid) {
                                     $where .= "AND sid='$sid' ";
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
                                     $stat = site_stat($where, 'requests');

                                     $data=array();
                                     if (is_array($sites)) {
                                     foreach ($sites as $ssid => $rows) {
                                     $data['legends'][] = $rows['title'];

                                     $data['title'][_SUBSCRIBERS][] = isset($stat[$ssid]['subscribers']) ? $stat[$ssid]['subscribers'] : 0;
                                     $data['title'][_CLICKS][] = isset($stat[$ssid]['subscribers']) ? $stat[$ssid]['clicks'] : 0;
                                     $data['title'][_MONEY][] = isset($stat[$ssid]['money']) ? $stat[$ssid]['money'] : 0;
                                     }
                                     }
                                     
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
                                    </select></div>
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
                                 <input name="m" type="hidden" value="sitestat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>
                            <div class="card-body">

                           <?php

                           echo chart_bar("barcode", $data);

                            ?>

                               <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="<?php echo _SITE ?>"><?php echo _SITE ?></th>
                                            <th title="<?php echo _REQUEST ?>"><?php echo _REQUEST ?></th>
                                            <th title="<?php echo _FAILURE ?>"><?php echo _FAILURE ?></th>
                                            <th title="<?php echo _SUBSCRIBERS ?>"><?php echo _SUBSCRIBERS ?></th>
                                            <th title="<?php echo _CRTITLE ?>">CR</th>
                                            <th title="<?php echo _UNSUBSCRIBERS ?>"><?php echo _UNSUBSCRIBERS ?></th>
                                            <th title="<?php echo _SENDED ?>"><?php echo _SENDED ?></th>
                                            <th title="<?php echo _VIEWS ?>"><?php echo _VIEWS ?></th>
                                            <th title="<?php echo _SENDBYUSERTITLE ?>"><?php echo _SENDEDBYUSER ?></th>
                                            <th title="<?php echo _CLICKS ?>"><?php echo _CLICKS ?></th>
                                            <th title="CTR">CTR</th>
                                            <th title="CPM">CPM</th>
                                            <th title="<?php echo _CLICKPRICETITLE ?>">CPC</th>
                                            <th title="<?php echo _MONEY ?>"><?php echo _MONEY ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     $all=array();
                                     if ($stat!=false) {
                                      foreach ($sites as $sid => $rows) {
                                        if (!$stat[$sid]['sended']) $stat[$sid]['sended']=0;
                                        if (!$stat[$sid]['requests']) $stat[$sid]['requests']=0;
                                        if (!$stat[$sid]['unsubs']) $stat[$sid]['unsubs']=0;
                                        if (!$stat[$sid]['money']) $stat[$sid]['money']=0;
                                        if (!$stat[$sid]['blocked_requests']) $stat[$sid]['blocked_requests']=0;
                                      	if ($stat[$sid]['subscribers']>0 && $stat[$sid]['requests']>0) $cr1 = round(($stat[$sid]['subscribers'] / $stat[$sid]['requests']) * 100, 1); else {$cr1=0; $stat[$sid]['subscribers']=0;}
                                      	if ($stat[$sid]['uniq_sended']>0) $usersended = round($stat[$sid]['sended'] / $stat[$sid]['uniq_sended'], 1); else {$usersended=0; $stat[$sid]['uniq_sended']=0;}
                                      	if ($stat[$sid]['clicks']>0 && $stat[$sid]['img_views']>0) $ctr = round(($stat[$sid]['clicks'] / $stat[$sid]['img_views']) * 100, 2); else {$ctr=0; $stat[$sid]['clicks']=0; }
                                      	if ($stat[$sid]['money']>0 && $stat[$sid]['sended']>0) $cpm = round(($stat[$sid]['money']/$stat[$sid]['sended'])*1000,2); else {$cpm=0; }
                                      	if ($stat[$sid]['money']>0 && $stat[$sid]['clicks']>0) $clickprice = round($stat[$sid]['money'] / $stat[$sid]['clicks'], 3); else $clickprice=0;
                                        if ($stat[$sid]['img_views']>0 && $stat[$sid]['sended']>0) $views_proc = round(($stat[$sid]['img_views'] / $stat[$sid]['sended'])*100, 0); else $views_proc=0;
                                        if (!$stat[$sid]['img_views']) $stat[$sid]['img_views']=0;
                                        if ($stat[$sid]['blocked_requests']>0 && $stat[$sid]['requests']>0) $blocked_proc = round(($stat[$sid]['blocked_requests'] / $stat[$sid]['requests'])*100, 0); else $blocked_proc=0;

                                        $title = $rows['title'];
                                        if ($stat[$sid]['money']) $stat[$sid]['money'] = moneyformat($stat[$sid]['money']);
                                        if ($cpm) $cpm = moneyformat($cpm);
                                        if ($clickprice) $clickprice = moneyformat($clickprice);

                                      	 echo "<tr>
                                            <td>№".$sid." ".$title."</td>
                                            <td>".$stat[$sid]['requests']."</td>
                                            <td>".$stat[$sid]['blocked_requests']."  <span class=table_proc title=\""._BLOCKEDPROC."\">".$blocked_proc."%</span></td>
                                            <td>".$stat[$sid]['subscribers']."</td>
                                            <td>".$cr1."</td>
                                            <td>".$stat[$sid]['unsubs']."</td>
                                            <td>".$stat[$sid]['sended']."</td>
                                            <td>".$stat[$sid]['img_views']." <span class=table_proc title=\""._VIEWSPROC."\">".$views_proc."%</span></td>
                                            <td>".$usersended."</td>
                                            <td>".$stat[$sid]['clicks']."</td>
                                            <td>".$ctr."%</td>
                                            <td>".$cpm."$</td>
                                            <td>".$clickprice."$</td>
                                            <td>".$stat[$sid]['money']."$</td>
                                        </tr>";

                                      }

                                      if ($stat['ALL']['subscribers']>0 && $stat['ALL']['requests']>0) $cr1 = round(($stat['ALL']['subscribers'] / $stat['ALL']['requests']) * 100, 1); else $cr1='-';
                                      if ($stat['ALL']['uniq_sended']>0) $usersended = round(($stat['ALL']['sended'] / $stat['ALL']['uniq_sended']) * 100, 1); else $usersended='-';
                                      if ($stat['ALL']['clicks']>0 && $stat['ALL']['img_views']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['img_views']) * 100, 2); else $ctr='-';
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['sended']>0) $cpm = round(($stat['ALL']['money']/$stat['ALL']['sended'])*1000,2); else $cpm=0;
                                      if ($stat['ALL']['money']>0 && $stat['ALL']['clicks']>0) $clickprice = round($stat['ALL']['money'] / $stat['ALL']['clicks'], 3); else $clickprice='-';
                                      if ($stat['ALL']['img_views']>0 && $stat['ALL']['sended']>0) $views_proc = round(($stat['ALL']['img_views'] / $stat['ALL']['sended'])*100, 0); else $views_proc=0;
                                      if ($stat['ALL']['blocked_requests']>0 && $stat['ALL']['requests']>0) $blocked_proc = round(($stat['ALL']['blocked_requests'] / $stat['ALL']['requests'])*100, 0); else $blocked_proc=0;

                                     }
                                     if ($stat['ALL']['money']) $stat['ALL']['money'] = moneyformat($stat['ALL']['money']);
                                     if ($cpm) $cpm = moneyformat($cpm);
                                     if ($clickprice) $clickprice = moneyformat($clickprice);
                                    ?>
                                    </tbody>
                                        <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['requests']) ? $stat['ALL']['requests'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['blocked_requests']) ? $stat['ALL']['blocked_requests'] : 0; ?> <span class=table_proc title="<?php echo _BLOCKEDPROC; ?>"><?php echo $blocked_proc; ?>%</span></th>
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
                                            <th><?php echo isset($stat['ALL']['money']) ? $stat['ALL']['money'] : 0; ?>$</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                     </div>
<script>
$('#basic-datatables').DataTable();
</script>
    <br />
     <div class="card-header">
     <strong class="card-title"><?php echo _ALLINFO ?></strong>
     </div>
 <?php
 $all=0;$unsubs=0; $active=0;
  if (is_array($sites)) {
foreach ($sites as $key => $row) {
    $all += $row['subscribers'];
    $unsubs += $row['unsubs'];
}
 if ($unsubs>0) {
 $allactive = $all - $unsubs;
 $active_proc = round(($allactive / $all) * 100, 0);
 } else {
 	$allactive = $all;
 	$active_proc = 100;
 	}

   }

   echo "<br />"._SUBSCRIBERSALL.": <b>".$all."</b> &nbsp;&nbsp; "._UNSUBSCRIBERSALL.": <b>".$unsubs."</b>  &nbsp;&nbsp; "._ACTIVE.": <b>".$allactive." (".$active_proc."%)</b><br />";
?>


<div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php echo _SITE ?></th>
                                            <th scope="col"><?php echo _SUBSCRIBERS2 ?></th>
                                            <th scope="col">% <?php echo _FROMALL ?></th>
                                            <th scope="col"><?php echo _ACTIVE ?></th>
                                            <th scope="col">% <?php echo _ACTIVE ?></th>
                                            <th scope="col"><?php echo _UNSUBSCRIBERSALL ?></th>
                                            <th scope="col">% <?php echo _UNSUBSCRIBERSALL ?></th>
                                            <th scope="col"><?php echo _LASTSUB ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $data=array();
                                     if (is_array($sites)) {
                                    foreach ($sites as $key => $row) {
                                            $data['title'][] = $row['title'];
                                            $data['data'][] = $row['subscribers'];
                                    	if ($row['subscribers']>0) $proc = round(($row['subscribers'] / $all) * 100, 0); else $proc=0;
                                    	if ($row['unsubs']>0) $active = $row['subscribers'] - $row['unsubs']; else $active=$row['subscribers'];
                                    	if ($active>0) $active_proc = round(($active / $row['subscribers']) * 100, 0); else $active_proc=0;
                                    	if ($row['unsubs']>0) $unsubs_proc = round(($row['unsubs'] / $unsubs) * 100, 0); else $unsubs_proc=0;
                                 echo "<tr>
                                            <th scope=\"row\">№".$key." ".$row['title']."</th>
                                            <td><a href=?m=subscribers&sid=".$key.">".$row['subscribers']."</a></td>
                                            <td>".$proc."%</td>
                                            <td>".$active."</td>
                                            <td>".$active_proc."%</td>
                                            <td>".$row['unsubs']."</td>
                                            <td>".$unsubs_proc."%</td>
                                            <td>".$row['last_subscribe']."</td>
                                        </tr>\n";
                                    }
                                    }
                                    ?>
                                    </tbody>
                                </table>

                                  <?php echo chart_pie("pieChart", $data); ?>

                            </div>

                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->