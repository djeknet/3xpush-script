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

function mails_stat($where='', $order='date', $limit=0) {
   global $db;
if ($limit) $limit = "limit $limit"; else $limit = '';
$sql = "SELECT date, sended, error_send, views, clicks, unsubs FROM mails_stat WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['date']]['sended'] += $value['sended'];
     $data[$value['date']]['error_send'] += $value['error_send'];
     $data[$value['date']]['views'] += $value['views'];
     $data[$value['date']]['clicks'] += $value['clicks'];
     $data[$value['date']]['unsubs'] += $value['unsubs'];

     $data['ALL']['sended'] += $value['sended'];
     $data['ALL']['error_send'] += $value['error_send'];
     $data['ALL']['views'] += $value['views'];
     $data['ALL']['clicks'] += $value['clicks'];
     $data['ALL']['unsubs'] += $value['unsubs'];

   }

   return $data;

  } else return false;

}

function feed_region_prices_group($where='') {
   global $db;

$sql = "SELECT feed_id, cc, mob, money, requests FROM feed_region_prices WHERE 1 ".$where." ORDER by date DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    foreach ($stat as $key => $value) {
        
      $price = round($value['money'] / $value['requests'], 4);
      
     $data[$value['feed_id']]['cc'][$value['cc']][$value['mob']]['summa'] += $price;
     $data[$value['feed_id']]['cc'][$value['cc']][$value['mob']]['count'] += 1;
   }

   return $data;

  } else return false;

}


function feed_region_prices($where='') {
   global $db;

$sql = "SELECT feed_id, cc, mob, money, requests FROM feed_region_prices WHERE 1 ".$where." ORDER by requests DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    foreach ($stat as $key => $value) {
      
     $data[$value['feed_id']]['cc'][$value['cc']][$value['mob']]['money'] += $value['money'];
     $data[$value['feed_id']]['cc'][$value['cc']][$value['mob']]['requests'] += $value['requests'];
   }

   return $data;

  } else return false;

}

function traf_exchange_stat_inout($sid, $where='', $order='views', $limit=0) {
   global $db;
   
if ($limit) $limit = "limit $limit"; else $limit = '';
$data = array();
$sql = "SELECT date, SUM(sended), SUM(clicks) FROM traf_exchange_stat WHERE admin_site IN (".$sid.") ".$where." Group by date ORDER by date DESC ".$limit."";
$stat_in = $db->sql_query($sql);
$stat_in = $db->sql_fetchrowset($stat_in);
 if (is_array($stat_in)) {
    foreach ($stat_in as $key => $value) {

     $data[$value['date']]['out']['sended'] += $value['SUM(sended)'];
     $data[$value['date']]['out']['clicks'] += $value['SUM(clicks)'];

     $data['ALL']['out']['sended'] += $value['SUM(sended)'];
     $data['ALL']['out']['clicks'] += $value['SUM(clicks)'];

   }
  } 

$sql = "SELECT date, SUM(sended), SUM(clicks) FROM traf_exchange_stat WHERE site_id IN (".$sid.") ".$where." Group by date ORDER by date DESC ".$limit."";
$stat_in = $db->sql_query($sql);
$stat_in = $db->sql_fetchrowset($stat_in);
 if (is_array($stat_in)) {
    foreach ($stat_in as $key => $value) {

     $data[$value['date']]['in']['sended'] += $value['SUM(sended)'];
     $data[$value['date']]['in']['clicks'] += $value['SUM(clicks)'];

     $data['ALL']['in']['sended'] += $value['SUM(sended)'];
     $data['ALL']['in']['clicks'] += $value['SUM(clicks)'];

   }
  } 
  
  return $data;
}


function traf_exchange_stat($where='', $order='views', $limit=0) {
   global $db;
if ($limit) $limit = "limit $limit"; else $limit = '';
$sql = "SELECT date, SUM(sended), SUM(clicks) FROM traf_exchange_stat WHERE 1 ".$where." Group by date ORDER by date DESC ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['date']]['sended'] += $value['SUM(sended)'];
     $data[$value['date']]['clicks'] += $value['SUM(clicks)'];

     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];

   }

   return $data;

  } else return false;

}

function refstat($where='') {
   global $db;
   
$sql = "SELECT * FROM refstat WHERE 1 ".$where."  ORDER by date DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    $all=1;
    foreach ($stat as $key => $value) {

     $data[$all]['date'] = $value['date'];
     $data[$all]['admin_id'] = $value['admin_id'];
     $data[$all]['money'] = $value['money'];
     $data[$all]['proc'] = $value['proc'];
     $data[$all]['active_users'] = $value['active_users'];
     $data[$all]['all_users'] = $value['all_users'];
     $all++;
   }

   return $data;

  } else return false;

}



function sysstat($where='') {
   global $db;
   
$sql = "SELECT id, date, data  FROM sysstat WHERE 1 ".$where."  ORDER by date DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    foreach ($stat as $key => $value) {
     $value['data'] = json_decode($value['data'], true);
     $data[$value['date']] = $value['data'];
   }

   return $data;

  } else return false;

}



function landstat($where='', $order='views') {
   global $db;
   
$sql = "SELECT land_id, SUM(views), SUM(subs), SUM(requests), SUM(blocked_requests)  FROM landing_stat WHERE 1 ".$where." GROUP BY land_id ORDER by SUM(".$order.") DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    foreach ($stat as $key => $value) {

     $data[$value['land_id']]['views'] += $value['SUM(views)'];
     $data[$value['land_id']]['subs'] += $value['SUM(subs)'];
     $data[$value['land_id']]['requests'] += $value['SUM(requests)'];
     $data[$value['land_id']]['blocked_requests'] += $value['SUM(blocked_requests)'];
     
     $data['ALL']['views'] += $value['SUM(views)'];
     $data['ALL']['subs'] += $value['SUM(subs)'];
     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['blocked_requests'] += $value['SUM(blocked_requests)'];

   }

   return $data;

  } else return false;

}


function prices($where='', $type = '', $days=0) {
   global $db;

$sql = "SELECT date, region, os_id, browser_id, lang, devtype, device, ip_range, views, cpc_money, cpv_money, cpv_views FROM advs_targets WHERE 1 ".$where."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    $data_arr = array();
    foreach ($stat as $key => $val) {
        
        if (!$val['cpc_money'] || !$val['cpv_money']) continue;
        
         if ($type == 'avg') {
        $data_arr['ALL']['cpc_money']+=$val['cpc_money'];
        $data_arr['ALL']['views']+=$val['views'];
        $data_arr['ALL']['cpv_money']+=$val['cpv_money'];
        $data_arr['ALL']['cpv_views']+=$val['cpv_views'];
            }
            
        if ($type == 'regions') {
            if ($days==1) {
        $data_arr['region'][$val['date']]['cpc_money']+=$val['cpc_money'];
        $data_arr['region'][$val['date']]['views']+=$val['views'];
        $data_arr['region'][$val['date']]['cpv_money']+=$val['cpv_money'];
        $data_arr['region'][$val['date']]['cpv_views']+=$val['cpv_views'];  
            } else { 
        $data_arr['region'][$val['region']]['cpc_money']+=$val['cpc_money'];
        $data_arr['region'][$val['region']]['views']+=$val['views'];
        $data_arr['region'][$val['region']]['cpv_money']+=$val['cpv_money'];
        $data_arr['region'][$val['region']]['cpv_views']+=$val['cpv_views'];
        }
        }
        
        if ($type == 'os') {  
        $data_arr['os'][$val['os_id']]['cpc_money']+=$val['cpc_money'];
        $data_arr['os'][$val['os_id']]['views']+=$val['views'];
        $data_arr['os'][$val['os_id']]['cpv_money']+=$val['cpv_money'];
        $data_arr['os'][$val['os_id']]['cpv_views']+=$val['cpv_views'];
        }
        
        if ($type == 'browser') { 
        $data_arr['browser'][$val['browser_id']]['cpc_money']+=$val['cpc_money'];
        $data_arr['browser'][$val['browser_id']]['views']+=$val['views'];
        $data_arr['browser'][$val['browser_id']]['cpv_money']+=$val['cpv_money'];
        $data_arr['browser'][$val['browser_id']]['cpv_views']+=$val['cpv_views'];
        }
        
        if ($type == 'lang') {
        $data_arr['lang'][$val['lang']]['cpc_money']+=$val['cpc_money'];
        $data_arr['lang'][$val['lang']]['views']+=$val['views'];
        $data_arr['lang'][$val['lang']]['cpv_money']+=$val['cpv_money'];
        $data_arr['lang'][$val['lang']]['cpv_views']+=$val['cpv_views'];
        }
        
        if ($type == 'devtype') {
        $data_arr['devtype'][$val['devtype']]['cpc_money']+=$val['cpc_money'];
        $data_arr['devtype'][$val['devtype']]['views']+=$val['views'];
        $data_arr['devtype'][$val['devtype']]['cpv_money']+=$val['cpv_money'];
        $data_arr['devtype'][$val['devtype']]['cpv_views']+=$val['cpv_views'];
        }
        
        if ($type == 'device') {
        $data_arr['device'][$val['device']]['cpc_money']+=$val['cpc_money'];
        $data_arr['device'][$val['device']]['views']+=$val['views'];
        $data_arr['device'][$val['device']]['cpv_money']+=$val['cpv_money'];
        $data_arr['device'][$val['device']]['cpv_views']+=$val['cpv_views'];
        }
        
        if ($type == 'ip_range') {
        $data_arr['ip_range'][$val['ip_range']]['cpc_money']+=$val['cpc_money'];
        $data_arr['ip_range'][$val['ip_range']]['views']+=$val['views'];
        $data_arr['ip_range'][$val['ip_range']]['cpv_money']+=$val['cpv_money'];
        $data_arr['ip_range'][$val['ip_range']]['cpv_views']+=$val['cpv_views'];
        }

   }
   if ($type == 'regions') {
   arsort($data_arr['region']);
   }
   if ($type == 'os') {
   arsort($data_arr['os_id']);
   }
   if ($type == 'browser') {
   arsort($data_arr['browser_id']);
   }
   if ($type == 'lang') {
   arsort($data_arr['lang']);
   }
   if ($type == 'devtype') {
   arsort($data_arr['devtype']);
   }
   if ($type == 'device') {
   arsort($data_arr['device']);
   }
   if ($type == 'ip_range') {
   arsort($data_arr['ip_range']);
   }
   return $data_arr;

  } else return false;

}


function chart_line2($id, $data) {
      if(!$id) $id='lineChart';
  if (!$data['legends']) $data['legends'] = array();
  if (!$data['title']) $data['title'] = array();
     $result = " <canvas id=\"".$id."\" height=\"70\" style=\"max-width: 1300px;\"></canvas>
    <script>
  var ctxL  = document.getElementById(\"".$id."\").getContext('2d');
  var myLineChart  = new Chart(ctxL , {
    type: 'line',
    data: {
      labels: ['".implode("','", $data['legends'])."'],
      datasets: [";

           foreach ($data['title'] as $title => $arr) {
        $datasets .= "{
        label: '".$title."',
        data: [".implode(",", $arr)."],
        backgroundColor: [";
        $colors='';
        foreach ($data['legends'] as $row) {
        $c1 = rand(31,201);
        $c2 = rand(32,202);
        $c3 = rand(33,203);
        $colors .= "'rgba(".$c1.", ".$c2.", ".$c3.", 0.4)',";
        }
        $colors = rtrim($colors, ',');
        $datasets .= $colors."],
        borderColor: [";
        $colors2='';
        foreach ($data['legends'] as $row) {
        $colors2 .= "'rgba(150, 150, 150, 1)',";
        }
        $colors2 = rtrim($colors2, ',');
        $datasets .= $colors2."],
        borderWidth: 1
        },";
      }
      $datasets = rtrim($datasets, ",");
      $result .= $datasets."]
    },
    options: {
      responsive: true,
      reverse:true
    }
  });

</script>\n";

return $result;

}

function chart_line($id, $data) {
      if(!$id) $id='lineChart';
     $colors[1]['back'] = "252, 247, 201";
     $colors[1]['border'] = "148, 134, 10";
     $colors[2]['back'] = "188, 233, 250";
     $colors[2]['border'] = "12, 118, 158";
     $colors[3]['back'] = "36, 193, 28";
     $colors[3]['border'] = "23, 131, 18";
     if (!is_array($data['title'])) $data['title'] = array();
     if (!is_array($data['data'][1])) $data['data'][1] = array();
     if (!is_array($data['data'][2])) $data['data'][2] = array();
     if (!is_array($data['data'][3])) $data['data'][3] = array();
     $result = " <canvas id=\"".$id."\" height=\"70\" style=\"max-width: 1177px;\"></canvas>
    <script>
  var ctxL  = document.getElementById(\"".$id."\").getContext('2d');
  var myLineChart  = new Chart(ctxL , {
    type: 'line',
    data: {
      labels: ['".implode("','", $data['title'])."'],
      datasets: [{
      	label: \""._REQUEST."\",
        data: [".implode(",", $data['data'][1])."],
         backgroundColor: [
            'rgba(".$colors[1]['back'].", .2)',
          ],
         borderColor: [
            'rgba(".$colors[1]['border'].", .7)',
          ],
          borderWidth: 2
    },
    {
      	label: \""._SUBSCRIBERS."\",
        data: [".implode(",", $data['data'][2])."],
         backgroundColor: [
            'rgba(".$colors[2]['back'].", .2)',
          ],
         borderColor: [
            'rgba(".$colors[2]['border'].", .7)',
          ],
          borderWidth: 2
        },
    {
      	label: \""._MONEY."\",
        data: [".implode(",", $data['data'][3])."],
         backgroundColor: [
            'rgba(".$colors[3]['back'].", .2)',
          ],
         borderColor: [
            'rgba(".$colors[3]['border'].", .7)',
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      reverse:true
    }
  });

</script>\n";

return $result;

}

function chart_pie($id, $data) {
      if(!$id) $id='pieChart';
      if (!empty($data['title'])) {
     $result = " <canvas id=\"".$id."\" height=\"70\" style=\"max-width: 1177px;\"></canvas>
    <script>
  var ctxP = document.getElementById(\"".$id."\").getContext('2d');
  var myPieChart = new Chart(ctxP, {
    type: 'pie',
    data: {
      labels: ['".implode("','", $data['title'])."'],
      datasets: [{
        data: [".implode(",", $data['data'])."],
        backgroundColor: [";
        foreach ($data['title'] as $row) {
        	$color = randomColor();
        	$colors .= "\"#".$color['hex']."\",";
        	}
      $colors = rtrim($colors, ',');
      $result .= $colors."],
      hoverBackgroundColor: [";
      foreach ($data['title'] as $row) {
            $color = randomColor();
        	$colors2 .= "\"#".$color['hex']."\",";
        	}
      $colors2 = rtrim($colors2, ',');
      $result .= $colors2."]
      }]
    },
    options: {
      responsive: true
    }
  });

</script>\n";

return $result;
    }
}

function chart_bar($id, $data, $colorsarr='') {
      if(!$id) $id='barChart';
      if (!$data['legends']) $data['legends'] = array();
      if (!$data['title']) $data['title'] = array();
     $result = "<canvas id=\"".$id."\" height=\"70\" style=\"max-width: 1136px;\"></canvas>
                            <script>
  //bar
  var ctxB = document.getElementById(\"".$id."\").getContext('2d');
  var myBarChart = new Chart(ctxB, {
    type: 'bar',
    data: {
      labels: ['".implode("','", $data['legends'])."'],
      datasets: [";
     $i=1;
      foreach ($data['title'] as $title => $arr) {
        $datasets .= "{
        label: '".$title."',
        data: [".implode(",", $arr)."],
        backgroundColor: [";
        $colors='';
        foreach ($data['legends'] as $row) {
         if ($colorsarr) {
         $c1 = $colorsarr[$i];   
         $c2 = $colorsarr[$i];   
         } elseif (is_int($row)) {
         $c1 = '108';
         $c2 = '171';    
         } else {
        $c1 = rand(10,255);
        $c2 = rand(10,255);
        }
        $colors .= "'rgba(".$c1.", ".$c2.", ".$c2.", 0.4)',";
        }
        $colors = rtrim($colors, ',');
        $datasets .= $colors."],
        borderColor: [";
        $colors2='';
        foreach ($data['legends'] as $row) {
        $colors2 .= "'rgba(255, 255, 255, 1)',";
        }
        $colors2 = rtrim($colors2, ',');
        $datasets .= $colors2."],
        borderWidth: 1
        },";
        $i++;
      }
      $datasets = rtrim($datasets, ",");
      $result .= $datasets."]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });

</script>\n";

return $result;

}

function region_stat($where='', $order='sended') {
   global $db;

$sql = "SELECT cc, SUM(cpv_money), SUM(closed), SUM(img_views), SUM(requests), SUM(subscribers), SUM(sended), SUM(clicks), SUM(money), SUM(`empty`), SUM(unsubs)  FROM region_stat WHERE 1 ".$where." Group by cc ORDER by SUM(".$order.") DESC";

$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['cc']]['requests'] += $value['SUM(requests)'];
     $data[$value['cc']]['sended'] += $value['SUM(sended)'];
     $data[$value['cc']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['cc']]['money'] += $value['SUM(money)'];
     $data[$value['cc']]['empty'] += $value['SUM(`empty`)'];
     $data[$value['cc']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['cc']]['subscribers'] += $value['SUM(subscribers)'];
     $data[$value['cc']]['img_views'] += $value['SUM(img_views)'];
     $data[$value['cc']]['closed'] += $value['SUM(closed)'];
     $data[$value['cc']]['cpv_money'] += $value['SUM(cpv_money)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['empty'] += $value['SUM(`empty`)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['subscribers'] += $value['SUM(subscribers)'];
     $data['ALL']['img_views'] += $value['SUM(img_views)'];
     $data['ALL']['closed'] += $value['SUM(closed)'];
     $data['ALL']['cpv_money'] += $value['SUM(cpv_money)'];
   }

   return $data;

  } else return false;

}

function feed_stat($where='', $order='') {
   global $db;

if (!$order) $order = 'sended';
$sql = "SELECT feed_id, SUM(requests), SUM(alltime), SUM(sended), SUM(clicks), SUM(money), SUM(`empty`), SUM(unsubs), SUM(wm_money) FROM feed_stat WHERE 1 ".$where." Group by feed_id ORDER by SUM(".$order.") DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['feed_id']]['requests'] += $value['SUM(requests)'];
     $data[$value['feed_id']]['alltime'] += $value['SUM(alltime)'];
     $data[$value['feed_id']]['sended'] += $value['SUM(sended)'];
     $data[$value['feed_id']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['feed_id']]['money'] += $value['SUM(money)'];
     $data[$value['feed_id']]['empty'] += $value['SUM(`empty`)'];
     $data[$value['feed_id']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['feed_id']]['wm_money'] += $value['SUM(wm_money)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['alltime'] += $value['SUM(alltime)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['empty'] += $value['SUM(`empty`)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['wm_money'] += $value['SUM(wm_money)'];
   }

   return $data;

  } else return false;

}

function subid_stat($where='', $limit='') {
   global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT subid, SUM(land_views), SUM(traf_cost), SUM(requests), SUM(subscribers), SUM(unsubs), SUM(sended), SUM(uniq_sended), SUM(img_views), SUM(clicks), SUM(money), SUM(blocked_requests), SUM(empty_send) FROM daystat WHERE subid!='' ".$where." Group by subid ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {
      if ($value['subid']==0) $value['subid']='-';
     $data[$value['subid']]['requests'] += $value['SUM(requests)'];
     $data[$value['subid']]['subscribers'] += $value['SUM(subscribers)'];
     $data[$value['subid']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['subid']]['sended'] += $value['SUM(sended)'];
     $data[$value['subid']]['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data[$value['subid']]['img_views'] += $value['SUM(img_views)'];
     $data[$value['subid']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['subid']]['money'] += $value['SUM(money)'];
     $data[$value['subid']]['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data[$value['subid']]['land_views'] += $value['SUM(land_views)'];
     $data[$value['subid']]['traf_cost'] += $value['SUM(traf_cost)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['subscribers'] += $value['SUM(subscribers)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data['ALL']['img_views'] += $value['SUM(img_views)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data['ALL']['land_views'] += $value['SUM(land_views)'];
     $data['ALL']['traf_cost'] += $value['SUM(traf_cost)'];
   }

   return $data;

  } else return false;

}

function day_stat($where='', $order='date', $offset=0, $filenum=100) {
   global $db;

$sql = "SELECT date, SUM(land_views), SUM(traf_cost), SUM(requests), SUM(subscribers), SUM(unsubs), SUM(sended), SUM(uniq_sended), SUM(img_views), SUM(clicks), SUM(money), SUM(blocked_requests), SUM(empty_send) FROM daystat WHERE 1 ".$where." Group by date ORDER by ".$order." DESC limit ".$offset.", ".$filenum."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['date']]['requests'] += $value['SUM(requests)'];
     $data[$value['date']]['subscribers'] += $value['SUM(subscribers)'];
     $data[$value['date']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['date']]['sended'] += $value['SUM(sended)'];
     $data[$value['date']]['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data[$value['date']]['img_views'] += $value['SUM(img_views)'];
     $data[$value['date']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['date']]['money'] += $value['SUM(money)'];
     $data[$value['date']]['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data[$value['date']]['land_views'] += $value['SUM(land_views)'];
     $data[$value['date']]['traf_cost'] += $value['SUM(traf_cost)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['subscribers'] += $value['SUM(subscribers)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data['ALL']['img_views'] += $value['SUM(img_views)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data['ALL']['land_views'] += $value['SUM(land_views)'];
     $data['ALL']['traf_cost'] += $value['SUM(traf_cost)'];
   }

   return $data;

  } else return false;

}

function browser_stat($where, $order='sended') {
   global $db;

$sql = "SELECT browser_id,  SUM(land_views),  SUM(traf_cost), SUM(requests), SUM(subscribers), SUM(unsubs), SUM(sended), SUM(uniq_sended), SUM(img_views), SUM(closed), SUM(clicks), SUM(money), SUM(blocked_requests), SUM(empty_send) FROM browser_stat WHERE 1 ".$where." Group by browser_id ORDER by SUM(".$order.") DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {
    foreach ($stat as $key => $value) {

     $data[$value['browser_id']]['requests'] += $value['SUM(requests)'];
     $data[$value['browser_id']]['subscribers'] += $value['SUM(subscribers)'];
     $data[$value['browser_id']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['browser_id']]['sended'] += $value['SUM(sended)'];
     $data[$value['browser_id']]['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data[$value['browser_id']]['img_views'] += $value['SUM(img_views)'];
     $data[$value['browser_id']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['browser_id']]['money'] += $value['SUM(money)'];
     $data[$value['browser_id']]['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data[$value['browser_id']]['land_views'] += $value['SUM(land_views)'];
     $data[$value['browser_id']]['traf_cost'] += $value['SUM(traf_cost)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['subscribers'] += $value['SUM(subscribers)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data['ALL']['img_views'] += $value['SUM(img_views)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['blocked_requests'] += $value['SUM(blocked_requests)'];
     $data['ALL']['land_views'] += $value['SUM(land_views)'];
     $data['ALL']['traf_cost'] += $value['SUM(traf_cost)'];
   }

   return $data;

  } else return false;

}

function site_stat($where, $order) {
   global $db;

if (!$order) $order = 'sended';
$sql = "SELECT sid, SUM(requests), SUM(subscribers), SUM(unsubs), SUM(sended), SUM(uniq_sended), SUM(img_views), SUM(clicks), SUM(money), SUM(blocked_requests), SUM(empty_send) FROM daystat WHERE 1 ".$where." Group by sid ORDER by SUM(".$order.") DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
    foreach ($stat as $key => $value) {

     $data[$value['sid']]['requests'] += $value['SUM(requests)'];
     $data[$value['sid']]['subscribers'] += $value['SUM(subscribers)'];
     $data[$value['sid']]['unsubs'] += $value['SUM(unsubs)'];
     $data[$value['sid']]['sended'] += $value['SUM(sended)'];
     $data[$value['sid']]['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data[$value['sid']]['img_views'] += $value['SUM(img_views)'];
     $data[$value['sid']]['clicks'] += $value['SUM(clicks)'];
     $data[$value['sid']]['money'] += $value['SUM(money)'];
     $data[$value['sid']]['blocked_requests'] += $value['SUM(blocked_requests)'];

     $data['ALL']['requests'] += $value['SUM(requests)'];
     $data['ALL']['subscribers'] += $value['SUM(subscribers)'];
     $data['ALL']['unsubs'] += $value['SUM(unsubs)'];
     $data['ALL']['sended'] += $value['SUM(sended)'];
     $data['ALL']['uniq_sended'] += $value['SUM(uniq_sended)'];
     $data['ALL']['img_views'] += $value['SUM(img_views)'];
     $data['ALL']['clicks'] += $value['SUM(clicks)'];
     $data['ALL']['money'] += $value['SUM(money)'];
     $data['ALL']['blocked_requests'] += $value['SUM(blocked_requests)'];
   }

   return $data;

  } else return false;

}

function subscribers_group($where='', $colum) {
   global $db;
   $colum = text_filter($colum);
$sql = "SELECT ".$colum.", COUNT(id) as count FROM subscribers WHERE 1 ".$where." GROUP BY ".$colum." ORDER by count DESC";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {
     $data = array();
    foreach ($stat as $row => $value) {
     $data[$value[$colum]] = $value['count'];
    }
    return $data;

  } else return false;

}

function subscribers_effect($where='') {
   global $db;
   if (!$order) $order = 'id';
$sql = "SELECT SUM(sended) as sended, SUM(views) as views, SUM(clicks) as clicks FROM subscribers WHERE 1 ".$where."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

   foreach ($stat as $key => $value) {
   
   $info['sended'] = $value['sended'];
   $info['views'] = $value['views'];
   $info['clicks'] = $value['clicks'];
   }
   
   $subs_effect = subs_effect($info);
   return $subs_effect;

  } else return false;

}

function subscribers($where='', $order='id', $limit=0) {
   global $db;
   if (!$order) $order = 'id';
if ($limit) $limit = "LIMIT ".$limit.""; else $limit = '';
$sql = "SELECT * FROM subscribers WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (is_array($stat)) {

   return $stat;

  } else return false;

}

function total_stat($where='') {
   global $db;
$sql = "SELECT * FROM total_stat WHERE 1 ".$where."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

    $data = array();
    foreach ($stat as $row => $value) {
     $data[$value['name']] = $value['value'];
    }
    return $data;

  } else return false;

}

function all_subscribers($where='') {
   global $db;

$sql = "SELECT COUNT(id) as count FROM subscribers WHERE 1 ".$where;
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrow($stat);
 if (count($stat)>0) {

   return $stat[0];

  } else return false;

}

function clickstat($where='', $order='id', $offset=0, $filenum=100) {
   global $db;

if (!$order) $order = 'id';
$sql = "SELECT * FROM clickstat WHERE 1 ".$where." ORDER by ".$order." DESC LIMIT ".$offset.", ".$filenum."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function all_clickstat($where='') {
   global $db;

$sql = "SELECT COUNT(id) as count FROM clickstat WHERE 1 ".$where;
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrow($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function send_report_hour($where='') {
   global $db;

$sql = "SELECT createtime, click_time FROM send_report WHERE 1 ".$where."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function send_report($where='', $order='id', $limit=0) {
   global $db;

if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM send_report WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function all_send_report($where='') {
   global $db;

$sql = "SELECT COUNT(id) as count FROM send_report WHERE 1 ".$where;
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrow($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function adv_stat($where='', $order='', $limit=0) {
   global $db;

if (!$order) $order = 'id';
if ($limit) $limit = "limit $limit"; else $limit = '';
$sql = "SELECT * FROM advs WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$stat = $db->sql_query($sql);
$stat = $db->sql_fetchrowset($stat);
 if (count($stat)>0) {

   return $stat;

  } else return false;

}

function group_stat($where='') {
    global $db, $SxGeo;

$data_arr = array();
$mbr = "Opera Mini,symbian,iphone,iemobile,pocket,sony,opera mobi,android,blackberry,smartphone,acs,alca,amoi,audi,aste,benq,bird,blac,blaz,brew,cell,cldc,cmd-,dang,doco,eric,hipt,inno,ipaq,java,jigs,kddi,keji,leno,lg-c,lg-d,lg-g,lg-a,lg-b,lg-c,lg-d,lg-f,lg-g,lg-k,lg-l,lg-m,lg-o,lg-p,lg-s,lg-t,lg-u,lg-w,lge-,maui,maxo,midp,mits,mmef,mobi,mot-,moto,mwbp,nec-,newt,noki,opwv,palm,pana,pant,pdxg,phil,play,pluc,prox,qtek,qwap,sage,sams,sany,sch-,sec-,send,seri,sgh-,shar,sie-,siem,smal,smar,sph-,symb,t-mo,teli,tim-,tsm-,upg1,upsi,vk-v,voda,wap-,wapa,wapi,wapp,wapr,webc,winw,xda,xda-,up.browser,up.link,windows ce,mini,mmp,wap,mobile,Android,hiptop,avantgo,plucker,xiino,blazer,elaine,iris,3g_t,vx1000,m800,e860,u940,ux840,compal,wireless,ahong,lg380,lgku,lgu900,lg210,lg47,lg920,lg840,lg370,sam-r,mg50,s55,g83,t66,vx400,mk99,d615,d763,el370,sl900,mp500,samu3,samu4,vx10,xda_,samu5,samu6,samu7,samu9,a615,b832,m881,s920,n210,s700,c-810,_h797,mob-x,sk16d,848b,mowser,s580,r800,471x,v120,rim8,c500foma:,160x,x160,480x,x640,t503,w839,i250,sprint,w398samr810,m5252,c7100,mt126,x225,s5330,s820,htil-g1,fly v71,s302,-x113,novarra,k610i,-three,8325rc,8352rc,sanyo,vx54,c888,nx250,n120,mtk ,c5588,s710,t880,c5005,i;458x,p404i,s210,c5100,teleca,s940,c500,s590,foma,samsu,vx8,vx9,a1000,_mms,myx,a700,gu1100,bc831,e300,ems100,me701,me702m-three,sd588,s800,ac831,mw200 ,d88,htc,htc_touch,355x,m50,km100,d736,p-9521,telco,sl74,ktouch,m4u,me702,phone,lg ,sonyericsson,samsung,240x,x320,vx10,nokia ,motorola,vodafone,o2,treo,1207,3gso,4thp,501i,502i,503i,504i,505i,506i,6310,6590,770s,802s,a wa,acer,airn,asus,attw,au-m,aur ,aus,abac,acoo,aiko,alco,anex,anny,anyw,aptu,arch,argo,bell,bw-n,bw-u,beck,bilb,c55,cdm-,chtm,capi,cond,craw,dall,dbte,dc-s,dica,ds-d,ds12,dait,devi,dmob,dopo,el49,erk0,esl8,ez40,ez60,ez70,ezos,ezze,elai,emul,ezwa,fake,fly-,fly_,g-mo,g1 u,g560,gf-5,grun,gene,go.w,good,grad,hcit,hd-m,hd-p,hd-t,hei-,hp i,hpip,hs-c ,htc-,htca,htcg,htcp,htcs,htct,htc_,haie,hita,huaw,hutc,i-20,i-go,i-ma,i230,iac,iac-,ig01,im1k,iris,jata,kgt,kpt ,kwc-,klon,lexi,lg g,lynx,m1-w,m3ga,mc01,mc21,mcca,medi,meri,mio8,mioa,mo01,mo02,mode,modo,mot ,mt50,mtp1,mtv ,mate,merc,motv,mozz,n100,n101,n102,n202,n203,n300,n302,n500,n502,n505,n700,n701,n710,nem-,newg,neon,netf,nzph,o2 x,o2-x,owg1,opti,oran,p800,pand,pg-1,pg-2,pg-3,pg-6,pg-8,pg-c,pg13,pn-2,pt-g,pire,pock,pose,psio,qa-a,qc-2,qc-3,qc-5,qc-7,qc07,qc12,qc21,qc32,qc60,qci-,qwap,r380,r600,raks,rim9,rove,sc01,scp-,se47,sec-,sec0,sec1,semc,sk-0,sl45,slid,smb3,smt5,sp01,spv ,spv-,sy01,samm,sava,scoo,smit,soft,sony,t218,t250,t600,t610,t618,tcl-,tdg-,telm,ts70,tsm3,tsm5,tx-9,tagt,talk,topl,hiba,up.b,utst,v400,v750,veri,vk40,vk50,vk52,vk53,vm40,vx98,virg,vite,vulc,w3c ,w3c-,wapj,wapu,wapm,wig ,wapv,wapy,waps,wapt,winc,wonu,x700,xda2,xdag,yas-,your,zte-,zeto,avan,brvw,bumb,ccwa,eml2,fetc,http,ibro,idea,ikom,jbro,jemu,kyoc,kyok,libw,m-cr,mywa,nok6,o2im,port,rozo,sama,sec-,sony,tosh,treo,vx52,vx53,vx60,vx61,vx70,vx80,vx81,vx83,vx85,whit,wmlb";
$mbr = explode(',', $mbr);

$sql = "SELECT browser, ip, referer, subid, del, subs_type, sended, device, brand, model FROM subscribers WHERE 1 ".$where."";
$subscribers = $db->sql_query($sql);
$subscribers = $db->sql_fetchrowset($subscribers);
$countsubscribers = count($subscribers);
if (is_array($subscribers)) {
foreach ($subscribers as $key => $val) {
     $data_arr['types'][$val['subs_type']]++;
     $data_arr['all']++;

	 $mobile=0;
	   foreach ($mbr as $key2 => $mbrowser) {
          if (preg_match('/' . $mbrowser . '/i', $val['browser']) == 1) {
          $mobile = 1;
          }
          }
       if ($mobile==1) {
       $data_arr['mobile']++;
       }

       $ipsel = explode(".", $val['ip']);
       $podset = $ipsel[0].".".$ipsel[1];


$agentdetail = getBrowser($val['browser']);
$browser1 = $agentdetail['name'];
$browserversion = $agentdetail['version'];
$platform = $agentdetail['platform'];
$browsershort = $agentdetail['short'];

$result= $SxGeo->getCityFull($val['ip']);
$usercountry = $result['country']['iso'];
 if (!$browsershort) $data_arr['agent']['noagent'] ++;
$data_arr['country'][$usercountry]['subs']++;
$data_arr['country'][$usercountry]['sended'] += $val['sended'];

$data_arr['agent'][$browsershort]['subs']++;
$data_arr['agent'][$browsershort]['sended'] += $val['sended'];

$data_arr['os'][$platform]['subs']++;
$data_arr['os'][$platform]['sended'] += $val['sended'];
$data_arr['device'][$val['device']]['subs']++;
$data_arr['device'][$val['device']]['sended'] += $val['sended'];

$brand_full = $val['brand'];//." ".$val['model'];
$data_arr['brand'][$brand_full]['subs']++;
$data_arr['brand'][$brand_full]['sended'] += $val['sended'];

$data_arr['network'][$podset]['subs']++;
$data_arr['network'][$podset]['sended'] += $val['sended'];

if ($val['del']==1) {
$data_arr['DEL']['country'][$usercountry] ++;
$data_arr['DEL']['agent'][$browsershort] ++;
$data_arr['DEL']['os'][$platform]++;
$data_arr['DEL']['mobile']++;
$data_arr['DEL']['types'][$val['sposob']] ++;
$data_arr['DEL']['fromsite'][$domain_ref]++;
$data_arr['DEL']['device'][$val['device']]++;
$data_arr['DEL']['brand'][$brand_full]++;
$data_arr['DEL']['network'][$podset]++;
}

if (!empty($val['referer'])) {
$parse = parse_url($val['referer']);
$domain_ref = $parse['host'];
$domain_ref = str_replace("www.", "", $domain_ref);
$data_arr['fromsite'][$domain_ref]++;
  } else {
$data_arr['fromsite']['noref']++;
  }

if (!empty($val['subid'])) {
$data_arr['sub'][$val['subid']]++;
}

}

arsort($data_arr['device']);
arsort($data_arr['brand']);
arsort($data_arr['country']);
arsort($data_arr['agent']);
arsort($data_arr['os']);
arsort($data_arr['fromsite']);
if (!empty($data_arr['subid']))  arsort($data_arr['subid']);
arsort($data_arr['types']);

return $data_arr;

} else return false;

}