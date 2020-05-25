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
                             $sid = intval($_GET['sid']);
                             $sid2 = intval($_GET['sid2']);
                             if ($sid2) $sid = $sid2;
                             $comment = text_filter($_GET['comment']);
                             $sended = intval($_GET['sended']);
                             $sended_type = intval($_GET['sended_type']);
                             $clicks = intval($_GET['clicks']);
                             $clicks_type = intval($_GET['clicks_type']);
                             $status = intval($_GET['status']);
                             $ip = text_filter($_GET['ip']);
                             $fid = intval($_GET['fid']);
                             $limit = intval($_GET['limit']);
                             $adv_id = intval($_GET['adv_id']);
                             $subid = text_filter($_GET['subid']);
                             $tag = text_filter($_GET['tag']);
                             $uid = intval($_GET['uid']);
                             if (!$start_date) $start_date = gettime($settings['days_stat']);
                             if ($uid==0) $uid = '';
                             if ($adv_id==0) $adv_id = '';

                                     if ($start_date && $end_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($start_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($end_date) {
                                     $where = "AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     }
                                     if ($update_from && $update_to) {
                                     $where .= "AND last_update >= '".converToTz($update_from, $config['proj_timezone'], $settings['timezone'])."' AND last_update <= '".converToTz($update_to, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($update_from) {
                                     $where .= "AND last_update >= '".converToTz($update_from, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($update_to) {
                                     $where .= "AND last_update <= '".converToTz($update_to, $config['proj_timezone'], $settings['timezone'])."' ";
                                     }
                                     
                                     if ($sid) {
                                     $where .= "AND sid='$sid' ";
                                     }
                                     if ($fid) {
                                     $where .= "AND feed_id='$fid' ";
                                     }
                                     if ($comment) {
                                     $where .= "AND comment LIKE '%$comment%' ";
                                     }
                                     if ($adv_id) {
                                     $where .= "AND feed_adv_id='$adv_id' ";
                                     }
                                     if ($subid) {
                                     $where .= "AND subid='$subid' ";
                                     }
                                     if ($tag) {
                                     $where .= "AND tag='$tag' ";
                                     }
                                     if ($uid) {
                                     $where .= "AND subscriber_id='$uid' ";
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

                                      if ($status==1) {
                                      $where .= "AND unsubs=0  ";
                                      $statussel[$status] = "selected";
                                      } elseif ($status==2) {
                                      $where .= "AND unsubs=1  ";
                                      $statussel[$status] = "selected";
                                      }
                                      if ($ip) {
                                      $where .= "AND ip LIKE '$ip%'  ";
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
                                      $stat = send_report($where, 'id', $limit);
                                      //$stat_all = send_report_hour($where, 'id');
                                      
                                          if (is_array($stat_all)) {
                                         foreach ($stat_all as $key => $value) { 
                                            $value['createtime'] = converToTz($value['createtime'], $settings['timezone']);
                                         $date = strtotime($value['createtime']);
                                        $hour = date('H', $date);
                                        if ($hour=="00") $hour=0; else $hour = ltrim($hour, 0);
                                        $hour_subs[$hour] += 1;
                                        if ($value['click_time']!='0000-00-00 00:00:00') {
                                           $value['click_time'] = converToTz($value['click_time'], $settings['timezone']); 
                                          $date = strtotime($value['click_time']);
                                        $hour = date('H', $date);
                                        if ($hour=="00") $hour=0; else $hour = ltrim($hour, 0);
                                        $hour_clicks[$hour] += 1;  
                                        }
                                        }
                                        for ($i=0;$i<24;$i++) {
                                        $subs = $hour_subs[$i];
                                        $clicks = $hour_clicks[$i];
                                        if (!$subs) $subs=0;
                                        $data['legends'][] = $i;
                                        $data['title'][_HOURSENDED][] = $subs;
                                        $data['title'][_HOURCLICKS][] = $clicks;
                                       }
                                        $colors[1] = '113';
                                        $colors[2] = '200';
                                        }
                                        
                                      if (is_array($stat)) {

                                        $allfound = count($stat);
                                      } else $allfound=0;
                                      
                                      if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }
                                       $feeds = feeds("AND admin_id=".$check_login['getid']."");
                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-2"><?php echo _SENDED3; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _CLICK; echo " "._FROM ?> <input type="text" name="update_from" id='datepicker3' value="<?php echo $update_from ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="update_to" id='datepicker4' value="<?php echo $update_to ?>" class="form-control form-control-sm"></div>
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
                                 <div class="col col-md-2">UID <input type="text" name="uid" value="<?php echo $uid ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">Subid <input type="text" name="subid" value="<?php echo $subid ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">Tag <input type="text" name="tag" value="<?php echo $tag ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _ADV ?> <input type="text" name="adv_id" value="<?php echo $adv_id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _COMMENT ?> <input type="text" name="comment" value="<?php echo $comment ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2 inlineblock"><?php echo _STATUS ?><br />
                                     <select name="status" class="form-control-sm form-control col col-md-8">
                                      <option value="0" <?php echo $statussel[0] ?>><?php echo _EVERY; ?></option>
                                      <option value="1" <?php echo $statussel[1] ?>><?php echo _STATUSACTIVE; ?></option>
                                      <option value="2" <?php echo $statussel[2] ?>><?php echo _STATUSUNSUBS; ?></option>
                                     </select></div>

                                     <div class="col col-md-2 inlineblock"><?php echo _SENDED ?><br />
                                     <select name="sended_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $sended_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $sended_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="sended" value="<?php echo $sended ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-2 inlineblock"><?php echo _CLICKS ?><br />
                                     <select name="clicks_type" class="form-control-sm form-control col col-md-4">
                                      <option value="0" <?php echo $clicks_typesel[0] ?>>&gt;</option>
                                      <option value="1" <?php echo $clicks_typesel[1] ?>>&lt;</option>
                                     </select>
                                     <input type="text" name="clicks" value="<?php echo $clicks ?>"  class="form-control form-control-sm col col-md-4"></div>
                                     <div class="col col-md-2 inlineblock"><?php echo _LINES ?><br />
                                     <select name="limit" class="form-control-sm form-control col col-md-8">
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
                                   $('#datepicker3').flatpickr({
                                       enableTime: true,
                                       dateFormat: "Y-m-d H:i",
                                       time_24hr: true,
                                       locale: "<?php echo $lang ?>"
                                 });
                                   $('#datepicker4').flatpickr({
                                       enableTime: true,
                                       dateFormat: "Y-m-d H:i",
                                       time_24hr: true,
                                       locale: "<?php echo $lang ?>"
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="sended">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp;&nbsp;      <?php echo _ALLFOUND.": <strong>".$allfound."</strong>"; ?> <br />
                             </form>
                             </div>

                            <div class="card-body">
                        <?php
                         if ($data) {
                           echo chart_bar("barcode", $data, $colors);
                           }

                            ?>
                               <div class="table-responsive">
                                <table id="basic-datatables" class="display infotable table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width=10% title="<?php echo _SENDED3." / "._CLICK; ?>"><?php echo _SENDED3." / "._CLICK; ?></th>
                                            <th title="<?php echo _SITE; ?>"><?php echo _SITE; ?></th>
                                            <th title="<?php echo _SUBSCRIBERS3; ?>"><?php echo _SUBSCRIBERS3; ?></th>
                                            <th width=25% title="<?php echo _ADV; ?>"><?php echo _ADV; ?></th>
                                            <th>BID</th>
                                            <th>CTR</th>
                                            <th title="<?php echo _LINK; ?>"><?php echo _LINK; ?></th>
                                            <th title="<?php echo _STAT; ?>"><?php echo _STAT; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $subs=array();
                                    $subscribers = subscribers("", 'id', 1000000);
                                    if (is_array($subscribers)) {
                                    foreach ($subscribers as $key => $val) {
	                                 $subs[$val['id']]['ip'] = $val['ip'];
	                                 $subs[$val['id']]['browser'] = $val['browser'];
                                     $subs[$val['id']]['os'] = $val['os'];
                                     $subs[$val['id']]['browser_short'] = $val['browser_short'];
                                     }
                                     }

                                     $feeds = feeds();

                                     if ($stat!=false) {
                                      foreach ($stat as $key => $value) {

                                      $ip = $subs[$value['subscriber_id']]['ip'];
                                      $br = $subs[$value['subscriber_id']]['browser'];

                                      $platform = $subs[$value['subscriber_id']]['os'];
                                      $browsershort = $subs[$value['subscriber_id']]['browser_short'];

                                      $result= $SxGeo->getCityFull($ip);
                                      $usercountry = $result['country']['iso'];

                                      if ($value['unsubs']==1) $status = "<b class=red>"._UNSUBSCRIBED."</b>"; else $status = "<b class=green>"._ACTIVE2."</b>";
                                      if ($value['clicks']>0) $clicks = "<br /><i class=\"menu-icon fa fa-hand-o-up\"></i> <a href=\"?m=clicks&advid=".$value['adv_id']."&subscriber_id=".$value['subscriber_id']."\" target=_blank><b class=green>CLICK</b></a>"; else $clicks='';

                                      $advarr = adv_stat("AND id='".$value['adv_id']."'");

                                           if (strlen($advarr[0]['url'])>30) {
    	                                   $ref = substr($advarr[0]['url'], 0, 30);
    	                                   $link = "<a href=/url.php?u=".$advarr[0]['url']." target=_blank>".$ref."...</a>";
    	                                   } else {
    	                                   $link = "<a href=/url.php?u=".$advarr[0]['url']." target=_blank>".$advarr[0]['url']."</a>";
    	                                   }

                                          if ($value['clicks'] > 0) {
                                            
                                            $minute = DateDiffInterval($value['createtime'], $value['click_time'], 'M'); 
                                            $minute = round($minute, 2);
                                            $hours = DateDiffInterval($value['createtime'], $value['click_time'], 'H'); 
                                            $hours = round($hours, 2);
                                            $hours = "<br /><br />".$minute." "._M." (".$hours." "._H.")";
                                            
                                            }else $hours='';
                                          $adv = "<img src=".$advarr[0]['icon']." width=50 align=left class=advimg> <b>".$advarr[0]['title']."</b><br />".$advarr[0]['description']."<br> <br><font class=small>#".$advarr[0]['hash']."</font>";

                                         
                                           if ($value['subid']) {
                                          $subid = "<br><br>Subid: ".$value['subid'];
                                          } else $subid='';
                                           if ($value['tag']) {
                                          $tag = "<br><br>Tag: ".$value['tag'];
                                          } else $tag='';

                                          
                                         if ($check_login['root']==1) { 
                                            $feed_name = $feeds[$value['feed_id']]['name'];
                                            $feed_name = 'feed '.$feed_name;
                                            } else $feed_name = '';
                                         
                                          if ($value['click_time']!="0000-00-00 00:00:00") {
                                            $click_time = converToTz($value['click_time'], $settings['timezone']);
                                           }
                                           
                                           if ($check_login['root']==1) {
                                            $bids = "<br /><br /><font class=small>min bid: ".$value['min_price']."<br />max bid: ".$value['max_price']."</font>";
                                            $ctrs = " <br /><br /><font class=small>min ctr: ".$value['min_ctr']."<br />max ctr: ".$value['max_ctr']."</font>";
                                           if ($value['comment']) {
                                          $comment = "<hr>"._COMMENT.": <br />".$value['comment'];
                                          } else $comment = '';
                                           }

                                      	 echo "<tr>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])." <br /><br /> ".$click_time."".$hours." </td>
                                            <td>".$value['sid']." <font class=small><a href=?m=sites&sid=".$value['sid']." target=_blank>info</a> ".$subid."</font> </td>
                                            <td>".$value['subscriber_id']." <a href=?m=subscribers&uid=".$value['subscriber_id']." target=_blank class=small>info</a><br />
                                            <font class=small2>".$ip." (".$usercountry.")<br />
                                            ".$platform.", ".$browsershort."".$tag."</font><br />".$status."</td>
                                            <td><font class=small>".$feed_name."</font> <br />".$adv."</td>
                                            <td>".$value['money']." ".$bids."</td>
                                            <td>".$value['ctr']." ".$ctrs."</td>
                                            <td>".$link."</td>
                                            <td><b>"._POSITION.":</b> ".$value['position']."<br />
                                            <b>"._ALLADVS.":</b> ".$value['all_advs']."<br />
                                            <b>"._HOURS.":</b> ".$value['hours']." ".$clicks." ".$comment."</td>
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
