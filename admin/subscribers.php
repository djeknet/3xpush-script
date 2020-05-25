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
<script type="text/javascript">
			function selectAll(){
				var items=document.getElementsByName('ids[]');
				for(var i=0; i<items.length; i++){
					if(items[i].type=='checkbox')
						items[i].checked=true;
				}
			}
			
			function UnSelectAll(){
				var items=document.getElementsByName('ids[]');
				for(var i=0; i<items.length; i++){
					if(items[i].type=='checkbox')
						items[i].checked=false;
				}
			}
            
</script>


<?php

$resettime = intval($_GET['resettime']);
$subsid = text_filter($_GET['subsid']);

// сброс времени след рассылки
if ($resettime==1 && $check_login['role']==1 && $subsid) {
$db->sql_query("UPDATE subscribers SET next_send=now() WHERE id IN (".$subsid.") AND admin_id='".$check_login['getid']."'"); 
status(_RESETTIME_OK, 'success');
}
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
                             $ref = text_filter($_GET['ref']);
                             $sended = intval($_GET['sended']);
                             $sended_type = intval($_GET['sended_type']);
                             $clicks = intval($_GET['clicks']);
                             $clicks_type = intval($_GET['clicks_type']);
                             $status = intval($_GET['status']);
                             $limit = intval($_GET['limit']);
                             $sdate_from = intval($_GET['sdate_from']);
                             $sdate_to = intval($_GET['sdate_to']);
                             
                             $ip = text_filter($_GET['ip']);
                             $subid = text_filter($_GET['subid']);
                             $tag = text_filter($_GET['tag']);
                             $uid = text_filter($_GET['uid']);
                             if ($subsid) $uid = $subsid;
                             $os = $_GET['os'];
                             $browser = $_GET['browser'];
                             $regions = $_GET['regions'];
                             $citys = $_GET['citys'];
                             $devices = $_GET['devices'];
                             $brands = $_GET['brands'];
                             $models = $_GET['models'];
                             $langs = $_GET['langs'];
                             $tags = $_GET['tags'];
                             $ranges = $_GET['ranges'];
                             $time = thetime();
                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                                     if ($start_date && $end_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($start_date) {
                                     $where = "AND createtime >= '".converToTz($start_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     } elseif ($end_date) {
                                     $where = "AND createtime <= '".converToTz($end_date, $config['proj_timezone'], $settings['timezone'])."' ";
                                     }
                                     
                                      if ($sdate_from && $sdate_to) {
                                     $where .= "AND createtime <= DATE_SUB('".$time."', INTERVAL ".intval($sdate_from)." DAY) AND createtime >= DATE_SUB('".$time."', INTERVAL ".intval($sdate_to)." DAY) ";
                                     } elseif ($sdate_from) {
                                     $where .= "AND createtime <= DATE_SUB('".$time."', INTERVAL ".intval($sdate_from)." DAY) ";
                                     } elseif ($sdate_to) {
                                     $where .= "AND createtime >= DATE_SUB('".$time."', INTERVAL ".intval($sdate_to)." DAY) ";
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
                                     if ($subid) {
                                     $where .= "AND subid='$subid' ";
                                     }
                                     if ($tag) {
                                     $where .= "AND tag='$tag' ";
                                     }
                                     if ($ref) {
                                     $where .= "AND referer LIKE '%$ref%' ";
                                     }
                                     if ($uid) {
                                     $where .= "AND id IN ('$uid') ";
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
                                      $where .= "AND del=0  ";
                                      $statussel[$status] = "selected";
                                      } elseif ($status==2) {
                                      $where .= "AND del=1  ";
                                      $statussel[$status] = "selected";
                                      }
                                      if ($ip) {
                                      $where .= "AND ip LIKE '$ip%'  ";
                                      }
                                      if ($os) {
                                        foreach ($os as $key => $val) {
                                            $os[] = text_filter($val);
                                        }
                                        $os = array_unique($os);
                                    $os1 = implode("','", $os);
                                    $where .= "AND os IN ('$os1') ";
                                     }
                                     if ($browser) {
                                        foreach ($browser as $key => $val) {
                                            $browser[] = text_filter($val);
                                        }
                                        $browser = array_unique($browser);
                                    $browser1 = implode("','", $browser);
                                    $where .= "AND browser_short IN ('$browser1') ";
                                     }
                                     if ($regions) {
                                        foreach ($regions as $key => $val) {
                                            $regions[] = text_filter($val);
                                        }
                                        $regions = array_unique($regions);
                                    $regions1 = implode("','", $regions);
                                    $where .= "AND cc IN ('$regions1') ";
                                     }
                                     if ($citys) {
                                        foreach ($citys as $key => $val) {
                                            $citys[] = text_filter($val);
                                        }
                                        $citys = array_unique($citys);
                                    $citys1 = implode("','", $citys);
                                    $where .= "AND city IN ('$citys1') ";
                                     }
                                     if ($devices) {
                                        foreach ($devices as $key => $val) {
                                            $devices[] = text_filter($val);
                                        }
                                        $devices = array_unique($devices);
                                    $devices1 = implode("','", $devices);
                                    $where .= "AND device IN ('$devices1') ";
                                     }
                                     if ($brands) {
                                        foreach ($brands as $key => $val) {
                                            $brands[] = text_filter($val);
                                        }
                                        $brands = array_unique($brands);
                                    $brands1 = implode("','", $brands);
                                    $where .= "AND brand IN ('$brands1') ";
                                     }
                                     if ($models) {
                                        foreach ($models as $key => $val) {
                                            $models[] = text_filter($val);
                                        }
                                        $models = array_unique($models);
                                    $models1 = implode("','", $models);
                                    $where .= "AND model IN ('$models1') ";
                                     }
                                     if ($langs) {
                                        foreach ($langs as $key => $val) {
                                            $langs[] = text_filter($val);
                                        }
                                        $langs = array_unique($langs);
                                    $langs1 = implode("','", $langs);
                                    $where .= "AND lang IN ('$langs1') ";
                                     }
                                     if ($tags) {
                                        foreach ($tags as $key => $val) {
                                            $tags[] = text_filter($val);
                                        }
                                        $tags = array_unique($tags);
                                    $tags1 = implode("','", $tags);
                                    $where .= "AND tag IN ('$tags1') ";
                                     }
                                     if ($ranges) {
                                        foreach ($ranges as $key => $val) {
                                            $ranges[] = intval($val);
                                        }
                                        $ranges = array_unique($ranges);
                                    $ranges1 = implode(",", $ranges);
                                    $where .= "AND ip_range IN ($ranges1) ";
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
                                      

                                      $stat = subscribers($where, '', $limit);
                                      $stat_all = subscribers($where, '');
                                      
                                       $data=array();
                                       $hour_subs=array();
                                        if (is_array($stat)) {

                                        $allfound = count($stat);
                                         foreach ($stat as $key => $value) {
                                        $os_list[$value['os']] = $value['os'];
                                        $browser_list[$value['browser_short']] = $value['browser_short'];
                                        $regions_list[$value['cc']] = $value['cc'];
                                        $citys_list[$value['city']] = $value['city'];
                                        $device_list[$value['device']] = $value['device'];
                                        $brand_list[$value['brand']] = $value['brand'];
                                        $model_list[$value['model']] = $value['model'];
                                        $tag_list[$value['tag']] = $value['tag'];
                                        $lang_list[$value['lang']] = $value['lang'];

                                       }
                                       
                                        foreach ($stat_all as $key => $value) {
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
                                        $data['title'][_HOURSUBS][] = $subs;
                                       }
                                      
                                     
                                      } else $allfound=0;


                                      $all_subscribers = all_subscribers("AND admin_id=".$check_login['getid']."");

                                      if (!$all_subscribers) $all_subscribers=0;
                                      $active_subscribers = all_subscribers("AND del=0 AND admin_id=".$check_login['getid']."");
                                      if ($active_subscribers) {
                                      	$active_proc = round(($active_subscribers/$all_subscribers)*100,0);
                                      } else $active_subscribers=0;
                                      
                                       if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }

                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-2"><?php echo _ADDED; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _UPDATE; echo " "._FROM ?> <input type="text" name="update_from" id='datepicker3' value="<?php echo $update_from ?>"  class="form-control form-control-sm"></div>
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
                                 <div class="col col-md-2">Subid <input type="text" name="subid" value="<?php echo $subid ?>"  class="form-control form-control-sm"></div>
                                 
                                 <div class="col col-md-2"><?php echo _REFERER ?> <input type="text" name="ref" value="<?php echo $ref ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">IP <input type="text" name="ip" value="<?php echo $ip ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">UID (,) <input type="text" name="uid" value="<?php echo $uid ?>"  class="form-control form-control-sm"></div>
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

                                    <div class="col col-lg-2"><?php echo _OS ?>
                                 <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="os[]">
                                    <option value=""></option>
                                        <?php
                                        foreach ($os_list as $key => $val) {
                                        if ($os && in_array($val, $os)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                                    <div class="col col-lg-2"><?php echo _BROWSERS ?>
                                 <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="browser[]">
                                    <option value=""></option>
                                        <?php

                                        foreach ($browser_list as $key => $val) {
                                        if ($browser && in_array($val, $browser)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                                     <div class="col col-lg-2"><?php echo _REGIONS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="regions[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($regions_list as $key => $val) {
                                        if ($regions && in_array($val, $regions)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                    <div class="col col-lg-2"><?php echo _CITYS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="citys[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($citys_list as $key => $val) {
                                        if ($citys && in_array($val, $citys)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                
                                   <div class="col col-lg-2"><?php echo _DEVICES ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="devices[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($device_list as $key => $val) {
                                        if ($devices && in_array($val, $devices)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                                                   <div class="col col-md-2 inlineblock"><?php echo _SUBSDATE2 ?><br />
                                 
                                   <input type="text" name="sdate_from" value="<?php echo $sdate_from ?>"  placeholder="<?php echo _FROM ?>" class="form-control form-control-sm col col-md-4"> 
                                   <input type="text" name="sdate_to" value="<?php echo $sdate_to ?>"  placeholder="<?php echo _TILL ?>" class="form-control form-control-sm col col-md-4">
                                   </div>
                                       </div>
                                    <div class="row form-group">
                                    <div class="col col-lg-2"><?php echo _BRANDS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="brands[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($brand_list as $key => $val) {
                                        if ($brands && in_array($val, $brands)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                    <div class="col col-lg-2"><?php echo _MODELS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="models[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($model_list as $key => $val) {
                                        if ($models && in_array($val, $models)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                   <div class="col col-lg-2"><?php echo _LANGS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="langs[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($lang_list as $key => $val) {
                                        if ($langs && in_array($val, $langs)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>
                                   <div class="col col-lg-2"><?php echo _TAGS ?>
                                     <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="select1" name="tags[]">
                                     <option value=""></option>
                                        <?php

                                        foreach ($tag_list as $key => $val) {
                                        if ($tags && in_array($val, $tags)) $sel ='selected'; else $sel='';
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>
                                   </div>

                                     <div class="col col-md-2 inlineblock"><?php echo _LINES ?><br />
                                     <select name="limit" class="form-control-sm form-control col col-md-8">
                                      <option value="100" <?php echo $limitsel[100] ?>>100</option>
                                      <option value="200" <?php echo $limitsel[200] ?>>200</option>
                                      <option value="500" <?php echo $limitsel[500] ?>>500</option>
                                      <option value="1000" <?php echo $limitsel[1000] ?>>1000</option>
                                     </select>
                                    </div>
                                </div>
<script src="vendors/chosen/chosen.jquery.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery(".select1").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "195px"
        });
         });
</script>
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
                                 <input name="m" type="hidden" value="subscribers">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>

                            <div class="card-body">
<form enctype="text/plain" method="post">
                                <?php echo _SUBSCRIBERSALL.": <b>".$all_subscribers."</b>&nbsp;&nbsp;"._ACTIVE.": <b>".$active_subscribers."</b> (".$active_proc."%) &nbsp;&nbsp; "._ALLFOUND.": <b>".$allfound."</b><br />"; ?>
                                 <?php

                           echo chart_bar("barcode", $data);

                            ?>

                             <div class="table-responsive">
                                <table id="basic-datatables" class="display infotable table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th title="number">#</th>
                                            <th width=13% title="<?php echo _ADDED." / "._UPDATE; ?>"><?php echo _ADDED." / "._UPDATE; ?></th>
                                            <th title="ip / tag">ip / tag</th>
                                            <th title="<?php echo _REGION; ?>"><?php echo _REGION; ?></th>
                                            <th title="<?php echo _BROWSER ." / "._OS; ?>"><?php echo _BROWSER ." / "._OS; ?></th>
                                            <th title="<?php echo _SITE; ?>"><?php echo _SITE; ?></th>
                                            <th title="<?php echo _SOURCE; ?>"><?php echo _SOURCE; ?></th>
                                            <th title="<?php echo _STAT; ?>"><?php echo _STAT; ?></th>
                                            <th title="<?php echo _STATUS; ?>"><?php echo _STATUS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                     $types = array(0 => 'https', 1 => 'redirect', 2 => 'block content', 3 => 'http');

                                     if ($stat!=false) {
                                      foreach ($stat as $key => $value) {

                                      $type_name = $types[$value['subs_type']];

                                      if ($value['del']==1) $status = "<b class=red>"._DELETED."</b>"; else $status = "<b class=green>"._ACTIVE2."</b>";
                                      $check = "<input type=\"checkbox\" value=\"".$value['id']."\" id=toggle name=\"ids[]\" />";

                                           if (strlen($value['referer'])>40) {
    	                                   $ref = substr($value['referer'], 0, 40);
    	                                   $ref = "<a href=/url.php?u=".$value['referer']." target=_blank>".$ref."...</a>";
    	                                   } else {
    	                                   $ref = "<a href=/url.php?u=".$value['referer']." target=_blank>".$value['referer']."</a>";
    	                                   }
                                           if ($value['tag']) $tag = "<br><br>Tag: ".$value['tag'].""; else $tag='';
                                           if ($value['last_update']!="0000-00-00 00:00:00") {
                                            $last_update = converToTz($value['last_update'], $settings['timezone']);
                                           }
                                           if (!$value['subid']) $value['subid'] = '-';
                                           if ($value['last_send']=='0000-00-00 00:00:00') {$value['last_send'] = '-';} else {
                                            $value['last_send'] = converToTz($value['last_send'], $settings['timezone']);
                                           }
                                           
                                           $next_send = converToTz($value['next_send'], $settings['timezone']);
                                           if ($value['del']==1) $next_send = '-';
                                           
                                      	 echo "<tr>
                                            <td><a href=?m=sended&uid=".$value['id']." target=_blank>#".$value['id']."</a> <br><br>".$check."</font></td>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])."<br /><br /><font class=small>".$last_update."</font></td>
                                            <td>".$value['ip']." ".$tag."</td>
                                            <td>".$value['cc']."<br /> ".$value['country']."<br /> ".$value['region']."<br /> ".$value['city']."</td>
                                            <td><span title='".$value['browser']."'>".$value['browser_short'].", ".$value['os']."</span> [".$value['lang']."]<br /><br />
                                            <font class=small2>device: ".$value['device']."<br />brand: ".$value['brand']." <br /> model: ".$value['model']."</font></td>
                                            <td><a href=?m=sites&sid=".$value['sid']." target=_blank>#".$value['sid']."</a><br /><br />
                                            <font class=small>subid: ".$value['subid']."</font><br /><br />
                                            ".$type_name."</td>
                                            <td>".$ref."</td>
                                            <td><b>"._LASTSEND.":</b> ".$value['last_send']."<br />
                                            <b>"._NEXTSEND.":</b> ".$next_send."<br />
                                            <b>"._NOADV.":</b> ".$value['empty']."<br />
                                            <b>"._SENDED2.":</b> ".$value['sended']."<br />
                                            <b>"._VIEWS2.":</b> ".$value['views']."<br />
                                            <b>"._CLICKS2.":</b> ".$value['clicks']."<br />
                                            <b>"._MONEY.":</b> ".$value['money']."</td>
                                            <td>".$status."</td>
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
                                
<a href="#" onclick='selectAll(); return false;'><i class="menu-icon fa fa-check-square-o"></i> <?php echo _CHOSENALL; ?></a> &nbsp; &nbsp; 
                             
 <strong><?php echo _CHOSEN; ?></strong> &nbsp; &nbsp;

<script type="text/javascript"><!--

function getSelectedChbox(frm) {
  var selchbox = [];        

  var inpfields = frm.getElementsByTagName('input');
  var nr_inpfields = inpfields.length;

  for(var i=0; i<nr_inpfields; i++) {
    if(inpfields[i].type == 'checkbox' && inpfields[i].checked == true) selchbox.push(inpfields[i].value);
  }

  return selchbox;
}
</script>
    
<script>
function asum(){
var n1=document.getElementById('all').value;
var n2=document.getElementById('price').value;
document.getElementById('allsumma').value=Number(n1)*Number(n2);
}
</script>
<script type="text/javascript">
function minmax(value, min, max) 
{
    if(parseInt(value) < min || isNaN(parseInt(value))) 
        return min; 
    else if(parseInt(value) > max) 
        return max; 
    else return value;
}
</script>
 
 <button type="button" name="popups" class="btn btn-primary" onclick="window.open('?m=my_send&new=1&subsid='+getSelectedChbox(this.form)); return false;"><i class="menu-icon fa fa-bullhorn"></i> <?php echo _CREATE_SEND; ?></button>
 <button type="button" class="btn btn-primary" onclick="window.location.assign('?m=subscribers&resettime=1&subsid='+getSelectedChbox(this.form)); return false;"><i class="menu-icon fa fa-clock-o"></i> <?php echo _RESETTIME; ?></button>
</form>

                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

<div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"  aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _SELLSUBS; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form name="buyform" action="?m=exchange" method="post">
                            <div class="modal-body">
                            
                               <div id="block-1">...</div>
                            
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" name="sell" id="Button1" value="1" class="btn btn-primary"><?php echo _SELL; ?></button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div> 
         