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
                             $ip = text_filter($_GET['ip']);
                             $fid = intval($_GET['fid']);
                             $subscriber_id = intval($_GET['subscriber_id']);
                             $limit = intval($_GET['limit']);
                             if (!$start_date) $start_date = gettime($settings['days_stat']);
                             if ($subscriber_id==0) $subscriber_id = '';

                                      if ($start_date && $end_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($start_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($end_date) {
                                     $where = "AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     }

                                     if ($sid) {
                                     $where .= "AND sid='$sid' ";
                                     }
                                     if ($fid) {
                                     $where .= "AND feed_id='$fid' ";
                                     }
                                     if ($ip) {
                                     $where .= "AND ip LIKE '$ip%'  ";
                                     }
                                     if ($subscriber_id) {
                                     $where .= "AND subscriber_id='$subscriber_id' ";
                                     }
                                     if (!$limit) $limit=100;
                                      $limitsel[$limit] = 'selected';
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
                                      $stat = clickstat($where, '', 0, $limit);
                                      $hour_subs = array();
                                        if (is_array($stat)) {
                                            
                                           foreach ($stat as $key => $value) { 
                                            $value['createtime'] = converToTz($value['createtime'], $settings['timezone']);
                                         $date = strtotime($value['createtime']);
                                        $hour = date('H', $date);
                                        if ($hour=="00") $hour=0; else $hour = ltrim($hour, 0);
                                        $hour_subs[$hour] += 1;
                                        }
                                        
                                         for ($i=0;$i<24;$i++) {
                                        $subs = $hour_subs[$i];
                                        if (!$subs) $subs=0;
                                        $data['legends'][] = $i;
                                        $data['title'][_HOURCLICKS][] = $subs;
                                       }


                                        $allfound = count($stat);
                                      } else $allfound=0;
                                      
                                      if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }
                                      
                                      $feeds = feeds();
                                     ?>
                             <div class="row form-group">
                                <div class="col col-md-2"><?php echo _TIME; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
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

                                 <div class="col col-md-2"><?php echo _SUBSCRIBERS3 ?> <input type="text" name="subscriber_id" value="<?php echo $subscriber_id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">IP <input type="text" name="ip" value="<?php echo $ip ?>"  class="form-control form-control-sm"></div>
                                  <div class="col col-md-3 inlineblock"><?php echo _LINES ?><br />
                                     <select name="limit" class="form-control-sm form-control col col-md-4">
                                      <option value="100" <?php echo $limitsel[100] ?>>100</option>
                                      <option value="500" <?php echo $limitsel[500] ?>>500</option>
                                      <option value="1000" <?php echo $limitsel[1000] ?>>1000</option>
                                      <option value="1000000" <?php echo $limitsel[1000000] ?>>1000000</option>
                                     </select>
                                    </div>
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
                                 <input name="m" type="hidden" value="clicks">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp;&nbsp;   <?php echo _ALLFOUND.": <strong>".$allfound."</strong>"; ?> <br />
                             </form>
                             </div>

                            <div class="card-body">
                      
                               <?php
                           echo chart_bar("barcode", $data);

                            ?>
                                 <div class="table-responsive">
                                <table id="basic-datatables" class="display infotable table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th title="<?php echo _TIME; ?>"><?php echo _TIME; ?></th>
                                            <th title="<?php echo _TIMEFROMSEND; ?>"><?php echo _TIMEFROMSEND; ?></th>
                                            <th title="<?php echo _SITE; ?>"><?php echo _SITE; ?></th>
                                            <th title="<?php echo _SUBSCRIBERS3; ?>"><?php echo _SUBSCRIBERS3; ?></th>
                                            <th title="<?php echo _ADV; ?>"><?php echo _ADV; ?></th>
                                            <th title="<?php echo _MONEY; ?>"><?php echo _MONEY; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($stat!=false) {
                                      foreach ($stat as $key => $value) {
                                        
                                    $subs=array();
                                    $subscribers = subscribers("AND id=".$value['subscriber_id']."", 'id', 1000000);
                                    if (is_array($subscribers)) {
                                    foreach ($subscribers as $key1 => $val) {
	                                 $ip = $val['ip'];
	                                 $br = $val['browser'];
                                     $platform = $val['os'];
                                     $browsershort = $val['browser_short'];
                                     $usercountry = $val['cc'];
                                     }
                                     }
                                      
                                      $advarr = adv_stat("AND id='".$value['advs_id']."'"); 
                                      $url = $advarr[0]['url'];
                                      $icon = $advarr[0]['icon'];
                                      $title = $advarr[0]['title'];
                                      $text = $advarr[0]['description'];
                                      

                                           if (strlen($url)>50) {
    	                                   $ref = substr($url, 0, 50);
    	                                   $link = "<a href=/url.php?u=".$url." target=_blank>".$ref."...</a>";
    	                                   } else {
    	                                   $link = "<a href=/url.php?u=".$url." target=_blank>".$url."</a>";
    	                                   }

                                          $adv = "<img src=".$icon." width=50 align=left class=advimg> <b>".$title."</b><br />".$text."";

                                          if ($value['minutes']>0) $hours = round($value['minutes'] / 60, 2) ; else $hours=0;
                                          $feed_name = $feeds[$value['feed_id']]['name'];
                                        if (!$feed_name) $feed_name = 'my ads'; elseif ($check_login['root']==1) $feed_name = 'feed '.$feed_name; else $feed_name = '';
                                         
                                         if ($value['cpv']!=1) {
                                            $info = "<a href=?m=sites&sid=".$value['sid']." target=_blank class=small>info</a>";
                                            $subinfo = "<a href=?m=subscribers&uid=".$value['subscriber_id']." target=_blank class=small>info</a>";
                                         } else {$info='';$subinfo='';}
                                         $value['money'] = moneyformat($value['money']);
                                         
                                         if ($check_login['root']==1) {
                                            $admin_info = "<span class=small>".$value['comment']."<br>clickid: ".$value['click_id']."</span><br>";
                                         } else $admin_info = '';

                                      	 echo "<tr>
                                            <td>".$value['id']."</td>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])."</td>
                                            <td>".$value['minutes']." "._M." (".$hours." "._H.")</td>
                                            <td>".$value['sid']." ".$info." </td>
                                            <td>".$value['subscriber_id']." ".$subinfo."<br />
                                            <font class=small2>".$ip." (".$usercountry.")<br />
                                            ".$platform.", ".$browsershort."</font></td>
                                            <td><font class=small>".$feed_name." #".$value['advs_id']."</font> <br />".$adv."<br />".$link."</td>
                                            <td>".$value['money']."$ <br>".$admin_info."</td>
                                        </tr>";
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
