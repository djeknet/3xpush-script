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

$all_subs = all_subscribers("AND admin_id=".$check_login['getid']."");
$all_active = all_subscribers("AND del=0 AND admin_id=".$check_login['getid']."");
$total_stat = total_stat("AND admin_id=".$check_login['getid']."");
if (!$total_stat['money']) $total_stat['money'] = 0;

if ($all_subs && $all_active) {
 $ctr = round(($all_active / $all_subs) * 100, 0);
}  else $ctr=0;

$news = news('AND date <= CURRENT_DATE()', 'date', 3);
?>


<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _DASHBOARD ?></h4>
</div>
                    

<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="card card-stats card-round">
								<div class="card-body ">
									<div class="row align-items-center">
										<div class="col-icon">
											<div class="icon-big text-center icon-primary bubble-shadow-small">
												<i class="flaticon-users"></i>
											</div>
										</div>
										<div class="col col-stats ml-3 ml-sm-0">
											<div class="numbers">
												<p class="card-category"><?php  echo _SUBSCRIBERSALLACTIVE; ?></p>
												<h4 class="card-title"><?php  echo $all_active; ?></h4>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="card card-stats card-round">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col-icon">
											<div class="icon-big text-center icon-info bubble-shadow-small">
												<i class="flaticon-next"></i>
											</div>
										</div>
										<div class="col col-stats ml-3 ml-sm-0">
											<div class="numbers">
												<p class="card-category"><?php  echo _HOMETEXT10; ?></p>
												<h4 class="card-title"><?php  echo round($total_stat['sended'], 0); ?></h4>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="card card-stats card-round">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col-icon">
											<div class="icon-big text-center icon-success bubble-shadow-small">
												<i class="flaticon-arrows-2"></i>
											</div>
										</div>
										<div class="col col-stats ml-3 ml-sm-0">
											<div class="numbers">
												<p class="card-category"><?php  echo _HOMETEXT11; ?></p>
												<h4 class="card-title"><?php  echo round($total_stat['clicks'], 0); ?></h4>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="card card-stats card-round">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col-icon">
											<div class="icon-big text-center icon-secondary bubble-shadow-small">
												<i class="flaticon-coins"></i>
											</div>
										</div>
										<div class="col col-stats ml-3 ml-sm-0">
											<div class="numbers">
												<p class="card-category"><?php  echo _HOMETEXT9; ?></p>
												<h4 class="card-title"><?php  echo $total_stat['money']; ?></h4>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>   
 
<div class="row">
<?php
if(is_array($news)) {
    $date = date("Y-m-d");
                 foreach ($news as $key => $value) {
                       $titles = json_decode($value['title'], true);
                       $content = json_decode($value['content'], true);
                      // $content[$lang] = htmlspecialchars($content[$lang]);
                      $content[$lang] = htmlspecialchars_decode($content[$lang], ENT_QUOTES);
                      $content[$lang] = text_filter($content[$lang]);
                       $contentwidth = stripslashes($content[$lang]);
            if (mb_strlen($contentwidth, "UTF-8") > 160) {
                $content[$lang] = mb_substr($content[$lang], 0, 160);
                $content[$lang] .= "... <i class=\"fa fa-angle-double-right\"></i>";
            }
            if ($value['date']==$date) {
                  $new = "<span class=\"badge badge-danger\">new</span>"; 
                  } else $new = "";
                  
               echo " <div class=\"col-12 col-sm-6 col-md-4\">
                        <div class=\"card\"  data-toggle=\"modal\" data-target=\"#news".$key."\" data-wow-duration=\"1s\" data-wow-delay=\"0.2s\">
                            <div class=\"card-header\">
                                <strong class=\"card-title\"><i class=\"fa fa-file-text-o\"></i> ".$titles[$lang]." <small>".$new."<span class=\"badge badge-success float-right mt-1\">".$value['date']."</span></small></strong>
                            </div>
                            <div class=\"card-body\">
                                <p class=\"card-text\">".$content[$lang]."</p>
                            </div>
                        </div>
                    </div>\n";
                    
                   }
}
?>
</div>
         <div class="content mt-3">
  <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                        <div class="card-header">
                                <strong class="card-title"><a href="?m=daystat"><?php  echo _HOMETEXT1; ?></a></strong>
                            </div>
                            <div class="card-body">
                            <?php
                            
                            if ($config['memcache_ip']) {
                            $code = 'homestat'.$check_login['getid'];
                            $stat = $memcached->get($code);    
                            }
                            if (!$stat) {
                            $stat['sites'] = $sites = sites("AND admin_id=".$check_login['getid']."", 'subscribers');
                            $stat['daystat'] = $daystat = day_stat("AND admin_id=".$check_login['getid']."", 'date', 0, 7);
                            $stat['regions_subs'] = $regions_subs = subscribers_group("AND del=0 AND admin_id=".$check_login['getid']."", 'cc');
                            $stat['devices_subs'] = $devices_subs = subscribers_group("AND del=0 AND admin_id=".$check_login['getid']."", 'device');
                            $stat['os_subs'] = $os_subs = subscribers_group("AND del=0 AND admin_id=".$check_login['getid']."", 'os');
                            $stat['send_report'] = $send_report = send_report("AND admin_id=".$check_login['getid']."", 'id', 5);
                            $stat['clickstat'] = $clickstat = clickstat("AND admin_id=".$check_login['getid']."", 'id', 0, 5);
                            $stat['feeds'] = $feeds = feeds("AND admin_id=".$check_login['getid']."");
                            $stat['subscribers'] = $subscribers = subscribers("AND admin_id=".$check_login['getid']."", 'id', 1000000);
                            if ($config['memcache_ip']) {
                            $memcached->set($code, $stat, false, time() + 300);
                            }
                            }
                            
                            $subs=array();
                            if (is_array($stat['subscribers'])) {
                            foreach ($stat['subscribers'] as $key => $val) {
	                        $subs[$val['id']]['ip'] = $val['ip'];
	                        $subs[$val['id']]['browser'] = $val['browser'];
	                        $subs[$val['id']]['cc'] = $val['cc'];
                            }
                            }
                          
                            if (is_array($stat['daystat'])) {
                            $stat['daystat'] = array_reverse($stat['daystat']);
                                     foreach ($stat['daystat'] as $date => $value) {
                                     if ($date!='ALL') {
                                     $data['title'][] = $date;
                                     $data['data'][1][] = $value['requests'];
                                     $data['data'][2][] = $value['subscribers'];
                                     $data['data'][3][] = $value['money'];
                                     }
                              }
                              echo chart_line("chart_line", $data);
                               } else {
                                            echo _NODATA;
                                        }


                            ?>
                            </div>
                        </div>
                    </div>

                      <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=sitestat"><?php  echo _HOMETEXT2; ?></a></strong>
                            </div>
                            <div class="card-body">

                                      <?php
                                        $data=array();
                                     if (is_array($stat['sites'])) {
                                     	$i=0;
                                      foreach ($stat['sites'] as $key => $row) {
                                      	if ($i==5) break;
                                            $i++;
                                            $data['title'][] = $row['title'];
                                            $data['data'][] = $row['subscribers'];
                                            }
                          echo chart_pie("pieChart", $data);
                                        } else {
                                            echo _NODATA;
                                        }
                                        ?>

                            </div>
                        </div>
                    </div>

                     <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=regionstat"><?php  echo _HOMETEXT4; ?></a></strong>
                            </div>
                            <div class="card-body">

                                      <?php
                                        $data=array();
                                     if (is_array($stat['regions_subs'])) {
                                     	$i=0;
                                      foreach ($stat['regions_subs'] as $region => $value) {
                                      	if ($i==5) break;
                                            $i++;
                                            $data['title'][] = $region;
                                            $data['data'][] = $value;
                                            }
                                       echo chart_pie("pieChart2", $data);
                                        } else {
                                            echo _NODATA;
                                        }
                                        ?>

                            </div>
                        </div>
                    </div>

                  <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=groupstat"><?php  echo _HOMETEXT5; ?></a></strong>
                            </div>
                            <div class="card-body">

                                      <?php
                                        $data=array();
                                     if (is_array($stat['devices_subs'])) {
                                     	$i=0;
                                      foreach ($stat['devices_subs'] as $key => $value) {
                                      	if ($i==5) break;
                                            $i++;
                                            $data['title'][] = $key;
                                            $data['data'][] = $value;
                                            }
                                       echo chart_pie("pieChart3", $data);
                                        } else {
                                            echo _NODATA;
                                        }
                                        ?>

                            </div>
                        </div>
                    </div>

                   <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=groupstat"><?php  echo _HOMETEXT6; ?></a></strong>
                            </div>
                            <div class="card-body">

                                      <?php
                                        $data=array();
                                     if (is_array($stat['os_subs'])) {
                                     	$i=0;
                                      foreach ($stat['os_subs'] as $key => $value) {
                                      	if ($i==5) break;
                                            $i++;
                                            $data['title'][] = $key;
                                            $data['data'][] = $value;
                                            }
                                       echo chart_pie("pieChart4", $data);
                                        } else {
                                            echo _NODATA;
                                        }
                                        ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=subscribers"><?php  echo _HOMETEXT3; ?></a></strong>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php  echo _TIME; ?></th>
                                            <th scope="col"><?php  echo _SITE; ?></th>
                                            <th scope="col"><?php  echo _REGION; ?></th>
                                            <th scope="col"><?php  echo _DEVICE; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                     if ($stat['subscribers']!=false) {
                                      $i=1;
                                      foreach ($stat['subscribers'] as $key => $value) {
                                      if ($i==5) break;
                                       $site = $stat['sites'][$value['sid']]['title'];
                                       if ($value['brand']) $model = "<br /><span class=small>".$value['brand']." ".$value['model']."</span>"; else $model='';
                                        echo "<tr>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])."</td>
                                            <td>".$site."</td>
                                            <td>".$value['cc']."</td>
                                            <td>".$value['os'].", ".$value['browser_short']."".$model."</td>
                                        </tr>";
                                        $i++;
                                        }
                                      } 
                                      ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                   <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=sended"><?php  echo _HOMETEXT7; ?></a></strong>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php  echo _TIME; ?></th>
                                            <th scope="col"><?php  echo _PUSHUSER; ?></th>
                                            <th scope="col"><?php  echo _ADV; ?></th>
                                            <th scope="col">BID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                     if ($stat['send_report']!=false) {
                                      $i=1;
                                      foreach ($stat['send_report'] as $key => $value) {
                                      $ip = $subs[$value['subscriber_id']]['ip'];
                                      $br = $subs[$value['subscriber_id']]['browser'];
                                      $cc = $subs[$value['subscriber_id']]['cc'];
                                      $agentdetail = getBrowser($br);
                                      $platform = $agentdetail['platform'];
                                      $browsershort = $agentdetail['short'];

                                       $advarr = adv_stat("AND id='".$value['adv_id']."'");
                                       $adv = "<img src=".$advarr[0]['icon']." width=50 align=left class=advimg> <b>".$advarr[0]['title']."</b><br />".$advarr[0]['description']."";

                                       if ($value['brand']) $model = "<br /><span class=small>".$value['brand']." ".$value['model']."</span>"; else $model='';
                                        echo "<tr>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])."</td>
                                            <td>#".$value['subscriber_id']." <a href=?m=subscribers&uid=".$value['subscriber_id']." target=_blank class=small>info</a><br />
                                            <font class=small2>".$ip." (".$cc.")<br />
                                            ".$platform.", ".$browsershort."</font></td>
                                            <td><font class=small>".$stat['feeds'][$value['feed_id']]['name']."</font> <br />".$adv."</td>
                                            <td>".$value['money']."</td>
                                        </tr>";
                                        }
                                      }
                                      ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><a href="?m=clicks"><?php  echo _HOMETEXT8; ?></a></strong>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr><th scope="col">№</th>
                                            <th scope="col"><?php  echo _TIME; ?></th>
                                            <th scope="col"><?php  echo _TIMEFROMSEND; ?></th>
                                            <th scope="col"><?php  echo _SITE; ?></th>
                                            <th scope="col"><?php  echo _SUBSCRIBERS3; ?></th>
                                            <th scope="col"><?php echo _MONEY; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                     if ($stat['clickstat']!=false) {
                                      $i=1;
                                      foreach ($stat['clickstat'] as $key => $value) {
                                      $ip = $subs[$value['subscriber_id']]['ip'];
                                      $br = $subs[$value['subscriber_id']]['browser'];

                                      $agentdetail = getBrowser($br);
                                      $platform = $agentdetail['platform'];
                                      $browsershort = $agentdetail['short'];

                                          
                                          if ($value['minutes']>0) $hours = round($value['minutes'] / 60, 2) ; else $hours=0;
                                      	 echo "<tr>
                                            <td>".$value['id']."</td>
                                            <td>".converToTz($value['createtime'], $settings['timezone'])."</td>
                                            <td>".$value['minutes']." "._M." (".$hours." "._H.")</td>
                                            <td>".$value['sid']." <a href=?m=sites&sid=".$value['sid']." target=_blank class=small>info</a> </td>
                                            <td>".$value['subscriber_id']." <a href=?m=subscribers&uid=".$value['subscriber_id']." target=_blank class=small>info</a><br />
                                            <font class=small2>".$ip." (".$value['cc'].")<br />
                                            ".$platform.", ".$browsershort."</font></td>
                                            <td>".$value['money']."</td>
                                        </tr>";
                                        }
                                      } 
                                      ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
    
     <?php
     if (is_array($news)) {
                foreach ($news as $key => $value) {
                       $titles = json_decode($value['title'], true);
                       $content = json_decode($value['content'], true);
                    $content = htmlspecialchars_decode($content[$lang], ENT_QUOTES);
        
         echo " <div class=\"modal fade\" id=\"news".$key."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"mediumModalLabel\"  aria-hidden=\"true\">
                    <div class=\"modal-dialog modal-lg\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"mediumModalLabel\">".$titles[$lang]."</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <form name=\"buyform\" action=\"?m=prices\" method=\"post\">
                            <div class=\"modal-body\">

                               ".$content."

                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">"._CANCEL."</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>\n";
                   }  
                   }    
       ?>            
