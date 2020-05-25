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

// get user language setting
function admin_lang($admin_id) {
    $settings = settings("AND admin_id=$admin_id");
    return $settings['lang'];
    }
    
// get user language setting by sending    
function myads_lang($id) {
    $myads = myads("AND id=$id");
    if (is_array($myads)) {
        $settings = settings("AND admin_id=".$myads[$id]['admin_id']."");
        if (!$settings['lang']) $settings['lang'] = 'en';
        return $settings['lang'];
    } else {
        return 'en';
    }
}
// get all users langs  
function admins_lang() {
$settings_all = settings_all();  
        $admins_lang=array();
        foreach ($settings_all as $key => $value) {
             if ($value['name']=='lang') {
              $admins_lang[$value['admin_id']] = $value['value'];  
             }
        } 
        
        return $admins_lang;  
}       
        
function sites_category($where='') {
global $db;
if (!$order) $order='id';
$sql = "SELECT * FROM sites_category WHERE 1 ".$where." ORDER by id ASC";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
    
$titles = json_decode($value['titles'], true);

foreach ($titles as $lang => $text) {
$data[$value['id']]['title'][$lang] = $text;
}

}
return $data;

} else return false;

}
function traf_exchange_admins($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM traf_exchange_admins WHERE 1 ".$where." ORDER by id DESC ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['traf_id'] = $value['traf_id'];
$data[$value['id']]['owner_id'] = $value['owner_id'];
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['site_id'] = $value['site_id'];
$data[$value['id']]['admin_site'] = $value['admin_site'];
$data[$value['id']]['sended'] = $value['sended'];
$data[$value['id']]['lock'] = $value['lock'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['max_send'] = $value['max_send'];
$data[$value['id']]['sended_today'] = $value['sended_today'];
}
return $data;

} else return false;

}


function traf_exchange($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM traf_exchange WHERE 1 ".$where." ORDER by id DESC ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['site_id'] = $value['site_id'];
$data[$value['id']]['file_name'] = $value['file_name'];
$data[$value['id']]['max_send'] = $value['max_send'];
$data[$value['id']]['today_send'] = $value['today_send'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['max_send_changed'] = $value['max_send_changed'];
}
return $data;

} else return false;

}

function check_exchange($data, $where='') {
    global $db;
    
    if (is_array($data)) {
    foreach ($data as $key => $value) {
       if ($value) {
       	 if ($key=='regions' && $value) {
       	    $value = implode("','", $value);
       	 	$where.= " AND b.cc IN ('".$value."') ";
       	 } elseif ($key=='langs' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.lang IN ('".$value."') ";
       	 } elseif ($key=='citys' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.city IN ('".$value."') ";
       	 } elseif ($key=='devicestype' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.device IN ('".$value."') ";
       	 } elseif ($key=='devices' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.brand IN ('".$value."') ";
       	 } elseif ($key=='sids' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.sid IN ('".$value."') ";
       	 } elseif ($key=='subid' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.subid IN ('".$value."') ";
       	 } elseif ($key=='tags' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.tag IN ('".$value."') ";
       	 } elseif ($key=='ranges' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.ip_range IN ('".$value."') ";
       	 } elseif ($key=='dates_from' && $value) {
       	 	 $where.= " AND b.createtime <= DATE_SUB('".$time."', INTERVAL ".intval($value)." DAY)  ";
       	 } elseif ($key=='dates_to' && $value) {
       	 	 $where.= " AND b.createtime >= DATE_SUB('".$time."', INTERVAL ".intval($value)." DAY)  ";
       	 }  elseif ($key=='os' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.os IN ('".$value."') ";
       	 }   elseif ($key=='browser' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND b.browser IN ('".$value."') ";
       	 } 
       }
    }
    }
    $total = exchange_sell("AND a.buyer_id=0 AND b.del=0  ".$where);
    return $total;
    
    }
    
function check_subscribers($data, $where_arr='', $type=0) {
    global $db;
    
    if (is_array($data)) {
    foreach ($data as $key => $value) {
       if ($value) {
       	 if ($key=='regions' && $value) {
       	    $value = implode("','", $value);
            if (strlen($value) >= 2) {
       	 	$where.= " AND cc IN ('".$value."') ";
            }
       	 } elseif ($key=='langs') {
       	 	 $value = implode("','", $value);
             if (strlen($value) >= 2) {
       	 	 $where.= " AND lang IN ('".$value."') ";
             }
       	 } elseif ($key=='citys' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND city IN ('".$value."') ";
       	 } elseif ($key=='devicestype' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND device IN ('".$value."') ";
       	 } elseif ($key=='devices' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND brand IN ('".$value."') ";
       	 } elseif ($key=='sids' && $value) {
       	 	 $value = implode("','", $value);
             if (strlen($value) >= 1) {
       	 	 $where.= " AND sid IN ('".$value."') ";
             }
       	 } elseif ($key=='subid' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND subid IN ('".$value."') ";
       	 } elseif ($key=='tags' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND tag IN ('".$value."') ";
       	 } elseif ($key=='ranges' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND ip_range IN ('".$value."') ";
       	 } elseif ($key=='dates_from' && $value) {
       	 	 $where.= " AND createtime <= DATE_SUB('".$time."', INTERVAL ".intval($value)." DAY)  ";
       	 } elseif ($key=='dates_to' && $value) {
       	 	 $where.= " AND createtime >= DATE_SUB('".$time."', INTERVAL ".intval($value)." DAY)  ";
       	 }  elseif ($key=='os' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND os IN ('".$value."') ";
       	 }   elseif ($key=='browser' && $value) {
       	 	 $value = implode("','", $value);
       	 	 $where.= " AND browser IN ('".$value."') ";
       	 }   elseif ($key=='subsid' && $value) {
       	 	 //$value = implode(",", $value);
       	 	 $where.= " AND id IN (".$value.") ";
       	 } 
       }
    }
    }
    
    if (is_array($where_arr)) {
        // проверяем связи по обмену трафиком, если такие есть, то учитываем их в кол-ве доступных подписчиков для рассылки
    if ($where_arr['admin_id']) {
    if ($where_arr['exchange']==1) $traf_exchange = traf_exchange_admins("AND admin_id=".$where_arr['admin_id']." AND status=1 AND sended_today < max_send AND `lock`=0");  
    if (is_array($traf_exchange)) {
        foreach ($traf_exchange as $key => $value) {
           
            if ($value['sended_today'] > 0) $limit = $value['max_send'] - $value['sended_today']; else $limit = $value['max_send'];
            
      if (!$type) {
      $plus_subs = subscribers("AND sid=".$value['site_id']." and del=0 ".$where, 'id', $limit);
      if (is_array($plus_subs)) $plus_subs = count($plus_subs); else $plus_subs = 0;
       } elseif ($type==1) { 
      $info_exh[] = subscribers("AND sid=".$value['site_id']." and del=0 ".$where, 'id', $limit);
      }
        }
  

      
    }
    $where .= "AND admin_id=".$where_arr['admin_id']." ";  
    
    }
    if ($where_arr['no_admins']) {
      $where .= "AND admin_id NOT IN (".$where_arr['no_admins'].") ";    
    }
    if ($where_arr['del']!=1) {
      $where .= "AND del=0 ";    
    }                             
     }   
                                  
    if (!$type) {
    $info = all_subscribers($where);
    if ($plus_subs) {
       $info = $info + $plus_subs; 
    }
    } elseif ($type==1) { 
    $info = subscribers($where);
    if (is_array($info_exh)) {
        if ($info==false) {
        $info = array();    
        }
          
      foreach ($info_exh as $key => $arr) {
        if (is_array($arr)) {
         foreach ($arr as $key2 => $value) {
        array_push($info, $value);
        }
        }
        }
        
    }
    }
    return $info;
    
    }
function users_ip() {
global $db;

$sql = "SELECT ip FROM admins";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {

    $net = subnet($value['ip'], 2);
    
     $data['ip'][$value['ip']] += 1;  
     $data['subnet'][$net] += 1;    
}
return $data;

} else return false;

}


function admins_balance($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit $limit"; else $limit='';
$sql = "SELECT * FROM admins as a 
LEFT JOIN balance as b ON (a.id=b.admin_id) WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit;

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['email'] = $value['email'];
$data[$value['id']]['login'] = $value['login'];
$data[$value['id']]['role'] = $value['role'];
$data[$value['id']]['last_login'] = $value['last_login'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['root'] = $value['root'];
$data[$value['id']]['owner_id'] = $value['owner_id'];
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['email_check'] = $value['email_check'];
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['ip'] = $value['ip'];
$data[$value['id']]['telegram'] = $value['telegram'];
$data[$value['id']]['get_mail'] = $value['get_mail'];
$data[$value['id']]['promo_mail'] = $value['promo_mail'];
$data[$value['id']]['auto_money'] = $value['auto_money'];
$data[$value['id']]['score_id'] = $value['score_id'];
$data[$value['id']]['reg_from'] = $value['reg_from'];
$data[$value['id']]['ref_active'] = $value['ref_active'];
$data[$value['id']]['summa'] = $value['summa'];
$data[$value['id']]['allmoney'] = $value['allmoney'];
$data[$value['id']]['last_sum'] = $value['last_sum'];
$data[$value['id']]['comission'] = $value['comission'];
$data[$value['id']]['deny_sending'] = $value['deny_sending'];
$data[$value['id']]['check_city'] = $value['check_city'];
$data[$value['id']]['is_support'] = $value['is_support'];
$data[$value['id']]['skype'] = $value['skype'];
$data[$value['id']]['good_user'] = $value['good_user'];
}
return $data;

} else return false;

}



// получение списка рефераллов для страницы Рефераллы
function referals($where='') {
global $db;

$sql = "SELECT a.owner, a.admin_id, a.money, a.date, b.reg_from, b.status, b.last_login, d.last_edit 
FROM referals AS a 
INNER  JOIN admins AS b ON (a.admin_id=b.id) 
INNER  JOIN balance AS d ON (a.admin_id=d.admin_id)  
WHERE 1 ".$where." ORDER by a.id DESC";
$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
     $data=array();
     $all=1;
      foreach ($info as $key => $value) {
        $data[$all]['owner'] = $value['owner'];
        $data[$all]['admin_id'] = $value['admin_id'];
        $data[$all]['money'] = $value['money'];
        $data[$all]['date'] = $value['date'];
        $data[$all]['reg_from'] = $value['reg_from'];
        $data[$all]['status'] = $value['status'];
        $data[$all]['last_login'] = $value['last_login'];
        $data[$all]['balance_edit'] = $value['last_edit'];
        $all++;
        }
    return $data;
    } else {
        return false;
    }
}
// функция получения истории расслок для подписчика, и сбора массива с хэшами объявлений, которые он уже получал
// по выставленным в общих и пользовательских настройках добавляем хэши в массив, затем при рассылке отфильтровываем их
function subs_history($subs_info) {
    global $db, $settings;
    if (!$settings) $settings = settings("AND admin_id=".$subs_info['admin_id']."");
    $today = date("Y-m-d H:i:s");

                $sql2 = "SELECT id, createtime, feed_hash FROM send_report WHERE subscriber_id='".$subs_info['id']."'";
                $history = $db->sql_query($sql2);
                $history = $db->sql_fetchrowset($history);


                $datetime2 = date_create($today);
                $nosendsub=0;
                $hashes = array();
                $history_count = array();

                 if (is_array($history)) {
                    foreach ($history as $key => $value) {
                        @$history_count[$value['feed_hash']]++;  
                        }
                foreach ($history as $key => $value) {

                    $datetime1 = date_create($value['createtime']);
                    $interval = date_diff($datetime1, $datetime2, true);
                    $days = $interval->days;


                if ($settings['user_messages']==1) {
                $hashes[] = $value['feed_hash'];
                } elseif ($history_count[$value['feed_hash']]>=$settings['user_messages'] && $days < $settings['user_messages_days'] ) {
                $hashes[] = $value['feed_hash'];
                } elseif ($history_count[$value['feed_hash']] >= $settings['max_send'] ) {
                $hashes[] = $value['feed_hash'];
                } elseif ($value['clicks']>0) {
                $hashes[] = $value['feed_hash'];
                }
                }
                $data['hashes'] = $hashes;
                $data['history_count'] = $history_count;
                $data['user_messages'] = $settings['user_messages'];
                $data['max_send'] = $settings['max_send'];
                $data['user_messages_days'] = $settings['user_messages_days'];
                
                return $data;
                } else {
                    return false;
                }
                
    }
    
function advs_info($admin_in=0) {
    global $db, $settings;
if ($admin_in) $where = "AND admin_id=$admin_in"; 
if (!$settings) $settings = settings($where);
 
$sql1 = "SELECT * FROM advs";
$adv_stat = $db->sql_query($sql1);
$adv_stat = $db->sql_fetchrowset($adv_stat);
$all_adv=count($adv_stat);
$adv_ctr = array();
$blocked=array();

if (is_array($adv_stat)) {
    
foreach ($adv_stat as $key => $val) {
    
if ($val['clicks']>10 && $val['sended']>$settings['max_adv_send']) {
$ctr = round(($val['clicks']/$val['sended'])*100, 3);
$adv_ctr[$val['hash']] = $ctr;
} elseif ($val['sended']>$settings['max_adv_send'] && !$val['clicks']) {
$adv_ctr[$val['hash']] = 0.00001;
} 

if ($val['blocked']==1 && $settings['block_unsubs']==1) {
$blocked[] = $val['hash'];
}

if ($val['unsubs']>0 && $settings['block_unsubs']==1) {
    $proc = round(($val['unsubs']/$val['uniq_sended'])*100, 3);
    if ($proc >= $settings['cr_block']) {
$blocked[] = $val['hash'];   
    }
}
} 

} 
$info['all_adv'] = $all_adv;
$info['adv_ctr'] = $adv_ctr;
$info['blocked'] = $blocked;

return $info;
    }
    
function gethash($hash) {
global $db;

$sql = "SELECT * FROM alerts_hash WHERE hash = '".$hash."'";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$hours =  DateDiffInterval($value['date'], '', 'H');
$data[$value['hash']]['date'] = $value['date'];
$data[$value['hash']]['hours'] = $hours;
}
return $data;

}   else return false;

}

function balancelist($where='') {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM balance WHERE 1 ".$where."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['admin_id']] = $value['summa'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function journal($where='', $limit='') {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM journal WHERE 1 ".$where." ORDER by id DESC ".$limit;

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['ip'] = $value['ip'];
$data[$value['id']]['cc'] = $value['cc'];
$data[$value['id']]['agent'] = $value['agent'];
$data[$value['id']]['page'] = $value['page'];
$data[$value['id']]['action'] = $value['action'];
$data[$value['id']]['error'] = $value['error'];
}
return $data;

} else return false;

}

function content_section($where='') {
global $db;

$sql = "SELECT * FROM content_section WHERE 1 ".$where."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['titles'] = $value['titles'];
$data[$value['id']]['sorts'] = $value['sorts'];
}
return $data;

} else return false;

}

function advs_urls_group($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT url, COUNT(id), GROUP_CONCAT(`advs_id`) AS ids, group_concat(`cc`) as cc, group_concat(`os_id`) as os_id  FROM advs_urls WHERE 1 ".$where." GROUP by url ORDER BY COUNT(id) DESC ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$cc = explode(',', $value['cc']);
$cc = array_unique($cc);
$os_id = explode(',', $value['os_id']);
$os_id = array_unique($os_id);

$ids = explode(',', $value['ids']);    
$ids = count($ids);

$data[$value['url']]['count'] = $value['COUNT(id)'];
$data[$value['url']]['ids'] = $ids;
$data[$value['url']]['cc'] = $cc;
$data[$value['url']]['os_id'] = $os_id;
}
return $data;

} else return false;

}

function sites_nopay($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM sites_nopay WHERE 1 ".$where." ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['sid']]['ALL'] += $value['clicks'];
$data[$value['sid']][$value['reason']] += $value['clicks'];
}
return $data;

} else return false;

}

function crons($name='') {
global $db;

$sql = "SELECT * FROM crons WHERE 1 ".$where."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['location'] = $value['location'];
$data[$value['id']]['cronfile'] = $value['cronfile'];
$data[$value['id']]['frequency'] = $value['frequency'];
$data[$value['id']]['is_stable'] = $value['is_stable'];
$data[$value['id']]['last_start'] = $value['last_start'];
$data[$value['id']]['last_end'] = $value['last_end'];
$data[$value['id']]['count_errors'] = $value['count_errors'];
$data[$value['id']]['count'] = $value['count'];
$data[$value['id']]['time'] = $value['time'];
$data[$value['id']]['description'] = $value['description'];
$data[$value['id']]['time_from'] = $value['time_from'];
$data[$value['id']]['time_to'] = $value['time_to'];
}
return $data;

} else return false;

}

function temp_table($name='') {
global $db;
if ($name) $where = "AND name='$name'";
$sql = "SELECT * FROM temp_table WHERE 1 ".$where."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['name']] = $value['value'];
}
return $data;

} else return false;

}



function brands() {
global $db;

$sql = "SELECT brand FROM subscribers group by brand";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['brand']] = $value['brand'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function devices() { 
$array = array('desktop', 'phablet', 'smartphone', 'tablet', 'tv'); 
return $array;
}

function advs_targets($advs_id) {
global $db;

$sql = "SELECT * FROM advs_targets WHERE advs_id=$advs_id";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data['date'] = $value['date'];
$data['region'][$value['region']] += $value['views'];
$data['os_id'][$value['os_id']] += $value['views'];
$data['browser_id'][$value['browser_id']] += $value['views'];
$data['lang'][$value['lang']] += $value['views'];
$data['devtype'][$value['devtype']] += $value['views'];
$data['device'][$value['device']] += $value['views'];
$data['ip_range'][$value['ip_range']] += $value['views'];
$data['show'] += $value['views'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}
function creativ_count($where='') {
global $db;

$sql = "SELECT a.id, COUNT(b.id) AS targets 
FROM advs AS a 
LEFT  JOIN advs_targets AS b ON (a.id=b.advs_id)
WHERE a.grab=1 ".$where."  
GROUP BY a.id 
HAVING targets >0";

$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();
$count = count($info);
if ($count) return $count; else return 0;  
}

function creativ($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT a.url, a.id, a.hash, a.icon, a.title, a.description, a.image, a.views, a.clicks, a.end_url, a.icon_hash, a.image_hash, SUM(b.views)  as sended, b.lang, b.device FROM advs AS a INNER  JOIN advs_targets AS b ON (a.id=b.advs_id) WHERE a.grab=1 ".$where." group by a.id  ORDER by ".$order." DESC ".$limit."";

$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['icon'] = $value['icon'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['description'] = $value['description'];
$data[$value['id']]['image'] = $value['image'];
$data[$value['id']]['views'] = $value['views'];
$data[$value['id']]['clicks'] = $value['clicks'];
$data[$value['id']]['sended'] = $value['sended'];
$data[$value['id']]['hash'] = $value['hash'];
$data[$value['id']]['end_url'] = $value['end_url'];
$data[$value['id']]['lang'] = $value['lang'];
$data[$value['id']]['device'] = $value['device'];
$data[$value['id']]['icon_hash'] = $value['icon_hash'];
$data[$value['id']]['image_hash'] = $value['image_hash'];
$data[$value['id']]['url'] = $value['url'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function feeds_templ($where='', $order='name') {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM feeds_templ WHERE 1 ".$where." ORDER by ".$order." ASC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['url'] = $value['url'];
$data[$value['id']]['feed_title'] = $value['feed_title'];
$data[$value['id']]['feed_body'] = $value['feed_body'];
$data[$value['id']]['feed_link_click_action'] = $value['feed_link_click_action'];
$data[$value['id']]['feed_link_icon'] = $value['feed_link_icon'];
$data[$value['id']]['feed_link_image'] = $value['feed_link_image'];
$data[$value['id']]['feed_bid'] = $value['feed_bid'];
$data[$value['id']]['convert_rate'] = $value['convert_rate'];
$data[$value['id']]['feed_winurl'] = $value['feed_winurl'];
$data[$value['id']]['site'] = $value['site'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['params'] = $value['params'];
$data[$value['id']]['feed_button1'] = $value['feed_button1'];
$data[$value['id']]['feed_button2'] = $value['feed_button2'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function content_name($name, $column) {
$content = content("AND name='$name' AND status=1");   
if (is_array($content)) {
foreach ($content as $key => $value) {
$arr .= htmlspecialchars_decode($value[$column], ENT_QUOTES);
}
return $arr;
} else return false;
}

function content($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM content WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['content'] = $value['content'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['views'] = $value['views'];
$data[$value['id']]['cteate_date'] = $value['cteate_date'];
$data[$value['id']]['pub_date'] = $value['pub_date'];
$data[$value['id']]['pageurl'] = $value['pageurl'];
$data[$value['id']]['code'] = $value['code'];
$data[$value['id']]['section_id'] = $value['section_id'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function reports($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM reports WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['description'] = $value['description'];
$data[$value['id']]['how_long'] = $value['how_long'];
$data[$value['id']]['cron_id'] = $value['cron_id'];
$data[$value['id']]['ip'] = $value['ip'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function landings($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM landings WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['category'] = $value['category'];
$data[$value['id']]['preview'] = $value['preview'];
$data[$value['id']]['html'] = $value['html'];
$data[$value['id']]['cteated'] = $value['cteated'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['views'] = $value['views'];
$data[$value['id']]['subs'] = $value['subs'];
$data[$value['id']]['used'] = $value['used'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function domains($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM domains WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['domain'] = $value['domain'];
$data[$value['id']]['updated'] = $value['updated'];
$data[$value['id']]['ssl_ready'] = $value['ssl_ready'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function news($where='', $order='date', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM news WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['content'] = $value['content'];
$data[$value['id']]['alert_sended'] = $value['alert_sended'];
$data[$value['id']]['send_alert'] = $value['send_alert'];
$data[$value['id']]['send_chat'] = $value['send_chat'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function faq($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM faq WHERE 1 ".$where." ORDER by sorts ASC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['answer'] = $value['answer'];
$data[$value['id']]['sorts'] = $value['sorts'];
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['keywords'] = $value['keywords'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function mails($where='', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM mails WHERE 1 ".$where." ORDER by id DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['email'] = $value['email'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['content'] = $value['content'];
$data[$value['id']]['create_time'] = $value['create_time'];
$data[$value['id']]['group_id'] = $value['group_id'];
$data[$value['id']]['lang'] = $value['lang'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['views'] = $value['views'];
$data[$value['id']]['clicks'] = $value['clicks'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function langslist () {
global $root;
if (($handle = fopen($root."include/langs.csv", "r")) !== FALSE) {
$row=0;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
$num = count($data);
$row++;
for ($c=0; $c < $num; $c++) {
$arr = explode(';', $data[$c]);
if ($arr[0]) {
$list[$row]['ru'] = $arr[0];
$list[$row]['en'] = $arr[1];
$list[$row]['iso'] = $arr[2];
$list[$row]['iso2'] = $arr[3];
}

}

}
fclose($handle);
return($list);
}
}
function isolist () {
global $root;
if (($handle = fopen($root."include/countries.csv", "r")) !== FALSE) {
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
$num = count($data);
$row++;
for ($c=0; $c < $num; $c++) {
$data[$c] = str_replace('"', '', $data[$c]);
$arr = explode(';', $data[$c]);
if ($arr[6]) {
$list[$row]['ru'] = $arr[2];
$list[$row]['en'] = $arr[4];
$list[$row]['iso'] = $arr[6];
}

}

}
fclose($handle);
return($list);
}
}

function get_onerow($column, $table, $where=1) {
global $db;

list($data) = $db->sql_fetchrow($db->sql_query("SELECT ".$column." FROM ".$table." WHERE ".$where." limit 1"));
if ($data) return $data; else return false;  
}

function admins_score($where='') {
global $db;

$sql = "SELECT * FROM admins_score WHERE 1 ".$where."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['payment_id'] = $value['payment_id'];
$data[$value['id']]['score'] = $value['score'];
$data[$value['id']]['check_code'] = $value['check_code'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}


function payments_type($where='') {
global $db;

$sql = "SELECT * FROM payment_type WHERE 1 ".$where." ORDER by id DESC";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['logo'] = $value['logo'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['summa'] = $value['summa'];
$data[$value['id']]['minsumma'] = $value['minsumma'];
$data[$value['id']]['comission'] = $value['comission'];
$data[$value['id']]['withdrowal'] = $value['withdrowal'];
$data[$value['id']]['texts'] = $value['texts'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}

function payments($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM payment WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";
$info = $db->sql_query($sql);// or $stop = $db->sql_error();
$info = $db->sql_fetchrowset($info);// or $stop = $db->sql_error();

if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['create_time'] = $value['create_time'];
$data[$value['id']]['update_time'] = $value['update_time'];
$data[$value['id']]['summa'] = $value['summa'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['out_id'] = $value['out_id'];
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['payment_type'] = $value['payment_type'];
$data[$value['id']]['ostatok'] = $value['ostatok'];
$data[$value['id']]['comment'] = $value['comment'];
$data[$value['id']]['out_ip'] = $value['out_ip'];
$data[$value['id']]['score'] = $value['score'];
$data[$value['id']]['spisano'] = $value['spisano'];
$data[$value['id']]['sys_info'] = $value['sys_info'];
$data[$value['id']]['check'] = $value['checksum'];
}
return $data;

} elseif($stop) {
return $stop; 
}  else return false;

}



function balance($admin_id) {
global $db;

list($summa) = $db->sql_fetchrow($db->sql_query("SELECT summa FROM balance WHERE admin_id=".$admin_id.""));
if (!$summa) $summa = 0;

return $summa;
}


function alerts_update($admin_id) {
global $db;
$db->sql_query('UPDATE alerts SET view=1 WHERE admin_id='.$admin_id.'');
}

function alerts($where='', $limit=5) {
global $db;

$sql = "SELECT * FROM alerts WHERE 1 ".$where." ORDER by id DESC limit ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['text'] = $value['text'];
$data[$value['id']]['view'] = $value['view'];
}
return $data;

} else return false;


}

function logout() {
unset($_COOKIE["p"]);
setcookie("p", null, -1, "/");
unset($_COOKIE["virtual_id"]);
setcookie("virtual_id", null, -1);
}

function check_login() {
global $db;

$hash = text_filter($_COOKIE["p"]);
@$virtual_id = intval($_COOKIE["vid"]);

if ($hash) {
$admin=array();
list($good_user, $email_check, $telegram, $id, $login, $role, $owner_id, $root, $email, $name, $ref_active) = $db->sql_fetchrow($db->sql_query("SELECT good_user, email_check, telegram, id, login, role, owner_id, root, email, name, ref_active FROM admins WHERE hash='$hash' and status=1"));
if ($login) {
$db->sql_query('UPDATE admins SET last_login=now() WHERE login="'.$login.'"') or $stop = mysql_error();
if ($owner_id==0) $owner_id = $id;

$admin['login'] = $login;
$admin['role'] = $role;
$admin['id'] = $id;
if ($virtual_id) {
$admin['getid'] = $virtual_id;  // если админ авторизовался под пользователем, то назначаем его id 
$admin['is_virtual'] = 1; 
$root=0; // отключаем рута, чтобы не выводить элементы для админа
} else {
$admin['getid'] = $owner_id;    
}
$admin['root'] = $root;
$admin['email'] = $email;
$admin['name'] = $name;
$admin['telegram'] = $telegram;
$admin['email_check'] = $email_check;
$admin['ref_active'] = $ref_active;
$admin['good_user'] = $good_user;
return $admin;
} else {
return false;
}
} else return false;

}


function check_admin($login, $pass, $rememberme) {
global $db, $config;

if ($login && $pass) {
$login = text_filter($login);
$pass = text_filter($pass);
$rememberme = intval($rememberme);

list($status, $id, $owner_id, $email_check, $old_city, $email, $check_city) = $db->sql_fetchrow($db->sql_query("SELECT status, id, owner_id, email_check, city, email, check_city FROM admins WHERE login='$login' AND pass=md5('".$pass."')"));
if ($status==1 && $email_check==1) {
    
$settings = settings("AND admin_id=$id");
if (!$settings['lang']) $lang = 'en'; else $lang = $settings['lang'];
    
if ($rememberme==1) {
$time = 86000 * 30;
} else $time = 86000;
$hash = md5(time().$pass);

$ip = getenv("REMOTE_ADDR");
$agent = getenv("HTTP_USER_AGENT");
$ip_arr = geoip_new($ip);
$agent_arr = getBrowser($agent);

// проверяем вход из другого места
if ($old_city && $old_city!=$ip_arr['city'] && $check_city==1) {
   $check_mail=1; 
}
if ($lang=='ru') {
    $text1 = "Попытка входа в аккаунт из нового места";
    $text2 = "Вход в аккаунт";
    $text3 = "Вход в гостевой аккаунт";
    $title = "Подтверждение входа с нового места";
    $content = "Попытка входа в ваш аккаунт с нового места: <br>
    Регион: <b>".$ip_arr['country'].", ".$ip_arr['city']."</b><br>
    IP: <b>".$ip."</b> <br><br>
    Если это вы, то подтвердите email переходом по ссылке";
    $not_you = "<br>---<br>Если это не вы, то не переходите по ссылке и поменяйте пароль от своего аккаунта!<br>Эту проверку можно отключить в настройках аккаунта.";
} else {
    $text1 = "Attempt to enter from a new location";
    $text2 = "Account login";
    $text3 = "Guest account login";
    $title = "Confirm enter from a new place";
    $content = "Attempting to log in to your account from a new location: <br>
    Region: <b>".$ip_arr['country'].", ".$ip_arr['city']."</b><br>
    IP: <b>".$ip."</b> <br>
    If this is you, then confirm the email by clicking on the link";
    $not_you = "<br>---<br>If this is not you, then do not follow the link and change the password from your account!<br>This check can be disabled in the account settings.";
}

if ($check_mail==1) {
    
jset($id, "$text1: ".$ip_arr['country'].", ".$ip_arr['city']." ($ip)"); 
alert("$text1! IP: $ip (".$ip_arr['country'].", ".$ip_arr['city']."), System: ".$agent_arr['platform'].", ".$agent_arr['name']."", $id);     

$check_code = md5($id.$ip_arr['city'].$config['global_secret']);
$city = base64_encode($ip_arr['city']);
$content .= "- <a href=https://".$settings['siteurl']."/admin/index.php?m=login&new_city=".$city."&check_code=".$check_code."&uid=".$id.">https://".$settings['siteurl']."/admin/index.php?m=login&new_city=".$city."&check_code=".$check_code."&uid=".$id."</a>  <br>";    
$content .= $not_you;

newmail($id, $email, $title, $content, $lang);
        
return 3;   
} else {
$db->sql_query('UPDATE admins SET hash="'.$hash.'", cc="'.$ip_arr['cc'].'", city="'.$ip_arr['city'].'" WHERE id="'.$id.'"');

jset($id, "$text2");

alert("$text2, IP: $ip (".$ip_arr['cc'].", ".$ip_arr['city']."), System: ".$agent_arr['platform'].", ".$agent_arr['name']."", $id);

// если это гостевой аккаунт, то отправляем уведомление владельцу
if ($owner_id) {
alert("$text3 - ".$login.", IP: $ip (".$ip_arr['cc'].", ".$ip_arr['city']."), System: ".$agent_arr['platform'].", ".$agent_arr['name']."", $owner_id);     
}
setcookie("p", $hash, time() + $time, "/");
return 1;
}
} elseif($status==1 && $email_check==0) {
return 2;
} else {
return false;
}
} else return false;

}

function admins($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit $limit"; else $limit='';
$sql = "SELECT * FROM admins WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit;

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['email'] = $value['email'];
$data[$value['id']]['login'] = $value['login'];
$data[$value['id']]['role'] = $value['role'];
$data[$value['id']]['last_login'] = $value['last_login'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['root'] = $value['root'];
$data[$value['id']]['owner_id'] = $value['owner_id'];
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['email_check'] = $value['email_check'];
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['ip'] = $value['ip'];
$data[$value['id']]['telegram'] = $value['telegram'];
$data[$value['id']]['get_mail'] = $value['get_mail'];
$data[$value['id']]['promo_mail'] = $value['promo_mail'];
$data[$value['id']]['auto_money'] = $value['auto_money'];
$data[$value['id']]['score_id'] = $value['score_id'];
$data[$value['id']]['reg_from'] = $value['reg_from'];
$data[$value['id']]['ref_active'] = $value['ref_active'];
$data[$value['id']]['deny_sending'] = $value['deny_sending'];
$data[$value['id']]['notif_teleg'] = $value['notif_teleg'];
$data[$value['id']]['notif_push'] = $value['notif_push'];
$data[$value['id']]['check_city'] = $value['check_city'];
$data[$value['id']]['is_support'] = $value['is_support'];
$data[$value['id']]['last_edit'] = $value['last_edit'];
$data[$value['id']]['skype'] = $value['skype'];
$data[$value['id']]['good_user'] = $value['good_user'];
}
return $data;

} else return false;

}



function sites($where='', $order='id', $limit=0) {
global $db;

if ($limit) $lim = "limit ".$limit."";  
$sql = "SELECT * FROM sites WHERE 1 ".$where." ORDER by ".$order." DESC ".$lim."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['url'] = $value['url'];
$data[$value['id']]['subscribers'] = $value['subscribers'];
$data[$value['id']]['unsubs'] = $value['unsubs'];
$data[$value['id']]['last_subscribe'] = $value['last_subscribe'];
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['type'] = $value['type'];
$data[$value['id']]['html'] = $value['html'];
$data[$value['id']]['land_options'] = $value['land_options'];
$data[$value['id']]['iframe_options'] = $value['iframe_options'];
$data[$value['id']]['postback'] = $value['postback'];
$data[$value['id']]['land_id'] = $value['land_id'];
$data[$value['id']]['partner_api'] = $value['partner_api'];
$data[$value['id']]['comission'] = $value['comission'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['request_limit'] = $value['request_limit'];
$data[$value['id']]['clickconf'] = $value['clickconf'];
$data[$value['id']]['cid_filter'] = $value['cid_filter'];
$data[$value['id']]['stopwords'] = $value['stopwords'];
$data[$value['id']]['category'] = $value['category'];
}
return $data;

} else return false;

}

function feeds($where='', $order='id') {
global $db;
if (!$order) $order='id';
$sql = "SELECT * FROM feeds WHERE 1 ".$where." ORDER by ".$order." DESC";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['url'] = $value['url'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['max_send'] = $value['max_send'];
$data[$value['id']]['total_sended'] = $value['total_sended'];
$data[$value['id']]['feed_title'] = $value['feed_title'];
$data[$value['id']]['feed_body'] = $value['feed_body'];
$data[$value['id']]['feed_link_click_action'] = $value['feed_link_click_action'];
$data[$value['id']]['feed_link_icon'] = $value['feed_link_icon'];
$data[$value['id']]['feed_link_image'] = $value['feed_link_image'];
$data[$value['id']]['feed_bid'] = $value['feed_bid'];
$data[$value['id']]['convert_rate'] = $value['convert_rate'];
$data[$value['id']]['feed_winurl'] = $value['feed_winurl'];
$data[$value['id']]['regions'] = $value['regions'];
$data[$value['id']]['coef'] = $value['coef'];
$data[$value['id']]['site'] = $value['site'];
$data[$value['id']]['feed_button1'] = $value['feed_button1'];
$data[$value['id']]['feed_button2'] = $value['feed_button2'];
$data[$value['id']]['timeout_next'] = $value['timeout_next'];
}
return $data;

} else return false;

}

function get_timezone ($timezone) {
$date = new DateTime("now", new DateTimeZone($timezone) );
$data['DATE'] = $date->format('Y-m-d');
$data['TIME'] = $date->format('H:i:s');
$data['H'] = $date->format('H');
$data['N'] = $date->format('N');
return $data;
}

function subs_myads($arr, $admin_id, $cpv_send=0, $cpс_send=0, $filters='', $dev=0) {
    global $config;
if (is_array($arr)) {
$settings_all = settings_all();
foreach ($settings_all as $key => $value) {
if ($value['name']=='timezone') {
$timezone_admins[$value['admin_id']] = $value['value']; // собираем массив с настройками локали админов для правильного таргета
}
}

$otl=array();

$blacklist = blacklist("AND admin_id=$admin_id");

if ($cpv_send==1 || $cpс_send==1) {
    
    if ($cpv_send==1 && $cpс_send==1) $where = "AND (a.cpv_on=1 OR a.cpc_on=1) "; 
    elseif ($cpv_send==1 && $cpс_send==0) $where = "AND a.cpv_on=1 "; 
    elseif ($cpv_send==0 && $cpс_send==1) $where = "AND a.cpc_on=1 "; 
    if ($filters) $where .= $filters; // добавляем дополнительные условия фильтрации сторонних объявлений
    
$myads = payon_ads("AND a.status=1 ".$where."");
if (is_array($myads)) $otl['all'] = count($myads); else $otl['all'] = 0;
}

if (is_array($myads)) {
foreach ($myads as $key => $val) {
$stop=0;

// фильтр по активности подписчиков
if ($val['user_activity']!=0) {
  $user_activity = user_activity($arr['views'], $arr['clicks'], 1.3);  
  if ($user_activity!=$val['user_activity']) {
    @$otl[$key] .= "user_activity: $user_activity<br>";
    continue;   
  }
}
// информация по подписчике
foreach ($arr as $name => $info) {

if ($name=='sid' && is_array($blacklist)) {
$adm_blocked_sids = $blacklist[$val['admin_id']]['blocked_sids'];
if ($adm_blocked_sids) {
$no_sids = explode(",", $adm_blocked_sids);
if (in_array($info, $no_sids)) {
@$otl[$key] .= "blocked_sids: $info<br>";
continue;    
}
}    
}
if ($name=='cc' && $val['regions']) {
$val['regions'] = explode (",", $val['regions']);
if (!in_array($info, $val['regions'])) {
$stop=1;
@$otl[$key] .= "country: ".$info."<br>";
continue;
}
}
if ($name=='city' && $val['citys']) {
$val['citys'] = region_city_json($val['citys']);
if ($val['citys'][$arr['cc']] && !in_array($info, $val['citys'][$arr['cc']])) { 
$stop=1;
@$otl[$key] .= "citys<br>";
continue;
}
}
if ($name=='region' && $val['country_regions']) {
$val['country_regions'] = region_city_json($val['country_regions']);
if ($val['country_regions'][$arr['cc']] && !in_array($info, $val['country_regions'][$arr['cc']])) { 
$stop=1;
@$otl[$key] .= "country regions<br>";
continue;
}
}
// фильтр по регионам
if ($name=='region' && $val['filter_regions']) {
$val['filter_regions'] = region_city_json($val['filter_regions']);
if ($val['filter_regions'][$arr['cc']] && in_array($info, $val['filter_regions'][$arr['cc']])) { 
$stop=1;
@$otl[$key] .= "filter_regions<br>";
continue;
}
}

if ($name=='lang' && $val['langs']) {
$langs = explode (",", $val['langs']);
if (!in_array($info, $langs)) {
$stop=1;
@$otl[$key] .= "langs<br>";
continue;
}
}
if ($name=='device' && $val['devtypes']) {
$devtypes = explode (",", $val['devtypes']);
if (!in_array($info, $devtypes)) {
$stop=1;
@$otl[$key] .= "devtypes<br>";
continue;
}
}
if ($name=='brand' && $val['devices']) {
$devices = explode (",", $val['devices']);
if (!in_array($info, $devices)) {
$stop=1;
@$otl[$key] .= "devices<br>";
continue;
}
}
if ($name=='os' && $val['os']) {
$os = explode (",", $val['os']);
if (!in_array($info, $os)) {
$stop=1;
@$otl[$key] .= "os<br>";
continue;
}
}
if ($name=='browser' && $val['browser']) {
$browser = explode (",", $val['browser']);
if (!in_array($info, $browser)) {
$stop=1;
@$otl[$key] .= "browser<br>";
continue;
}
}

if ($name=='sid' && $val['sids']) {
$sids = explode (",", $val['sids']);
if (!in_array($info, $sids)) {
$stop=1;
@$otl[$key] .= "sids<br>";
continue;
}
}
if ($name=='subid' && $val['subid']) {
$subid = explode (",", $val['subid']);
if (!in_array($info, $subid)) {
$stop=1;
@$otl[$key] .= "subid<br>";
continue;
}
}
if ($name=='tag' && $val['tags']) {
$tags = explode (",", $val['tags']);
if (!in_array($info, $tags)) {
$stop=1;
@$otl[$key] .= "tags<br>";
continue;
}
}
if ($name=='ip_range' && $val['ip_ranges']) {
$ip_ranges = explode (",", $val['ip_ranges']);
if (!in_array($info, $ip_ranges)) {
$stop=1;
@$otl[$key] .= "ip_range<br>";
continue;
}
}
if ($name=='dates' && $val['dates_from']) {
if ($info < $val['dates_from']) {
$stop=1;
@$otl[$key] .= "dates_from<br>";
continue;
}
}
if ($name=='dates' && $val['dates_to']) {
if ($info > $val['dates_to']) {
$stop=1;
@$otl[$key] .= "dates_to<br>";
continue;
}
}
// фильтр по ip
if ($name=='ip' && $val['filters']) {
  $filters = json_decode($val['filters'], true);
                // фильтр по ip   
                if ($filters['ip']) {
	    	    $filter_ip = explode (",", $filters['ip']);
	    	    if (in_array($info, $filter_ip)) {
                $stop=1;
                @$otl[$key] .= "ip filter 1<br>";
                continue;
	    	    } else {
	    	      $ip_int = ip2long($info);
	    	       foreach ($filter_ip as $n => $val) {
	    	          // диапазон ip
                       if (stripos($val, "-") !== false) {
                        $ip_diap = explode ("-", $val);
                        $from_ip = ip2long($ip_diap[0]);
                        $to_ip = ip2long($ip_diap[1]);
                        if ($ip_int >= $from_ip && $ip_int <= $to_ip) {
                         $stop=1;
                @$otl[$key] .= "ip filter 2<br>";
                continue;    
                        }
                        } elseif (stripos($info, $val) !== false) { // или подсеть
                         $stop=1;
                @$otl[$key] .= "ip filter 3<br>";
                continue; 
                        }
                   }
	    	    }
                } 
                
	    	  
}

// время пользователя подписчика за условие
if ($name=='timezone' && $val['time_type']==1 && $val['hours_on'] && $val['days_on'] && $info) {
$data = get_timezone ($info);
$hours_on = explode (",", $val['hours_on']);
$days_on = explode (",", $val['days_on']);
$data['H'] = ltrim($data['H'], 0);
if (!$data['H']) $data['H']=0;
if (!in_array($data['H'], $hours_on)) {
$stop=1;
@$otl[$key] .= "hours_on<br>";
continue;
}
if (!in_array($data['N'], $days_on)) {
$stop=1;
@$otl[$key] .= "days_on<br>";
continue;
}
}
}


// локальное время админа
if ($name=='timezone' && $val['time_type']==0 && $val['hours_on'] && $val['days_on']) {
    if (!$timezone_admins[$val['admin_id']]) {
    $timezone_admins[$val['admin_id']] = $config['proj_timezone'];
}
$data = get_timezone ($timezone_admins[$val['admin_id']]);
$hours_on = explode (",", $val['hours_on']);
$days_on = explode (",", $val['days_on']);
$data['H'] = ltrim($data['H'], 0);
if (!$data['H']) $data['H']=0;
if (!in_array($data['H'], $hours_on)) {
$stop=1;
@$otl[$key] .= "hours_on 2<br>";
}
if (!in_array($data['N'], $days_on)) {
$stop=1;
@$otl[$key] .= "days_on 2<br>";
}
}

if ($stop==1) {
unset($myads[$key]);
//print_r($otl);
}
}
}
if ($dev==1) {
 return $otl;   
}
if (is_array($myads)) {
return $myads;
} else return false;
}
}

function myads($where='', $order='id', $limit=0) {
global $db;
if ($limit) $limit = "limit ".$limit.""; else $limit='';
$sql = "SELECT * FROM myads WHERE 1 ".$where." ORDER by ".$order." DESC ".$limit."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['id'] = $value['id'];
$data[$value['id']]['admin_id'] = $value['admin_id'];
$data[$value['id']]['date'] = $value['date'];
$data[$value['id']]['time'] = $value['time'];
$data[$value['id']]['cid'] = $value['cid'];
$data[$value['id']]['title'] = $value['title'];
$data[$value['id']]['text'] = $value['text'];
$data[$value['id']]['icon'] = $value['icon'];
$data[$value['id']]['image'] = $value['image'];
$data[$value['id']]['url'] = $value['url'];
$data[$value['id']]['regions'] = $value['regions'];
$data[$value['id']]['langs'] = $value['langs'];
$data[$value['id']]['sended'] = $value['sended'];
$data[$value['id']]['last_send'] = $value['last_send'];
$data[$value['id']]['views'] = $value['views'];
$data[$value['id']]['clicks'] = $value['clicks'];
$data[$value['id']]['last_click'] = $value['last_click'];
$data[$value['id']]['status'] = $value['status'];
$data[$value['id']]['maxsend'] = $value['maxsend'];
$data[$value['id']]['sids'] = $value['sids'];
$data[$value['id']]['tags'] = $value['tags'];
$data[$value['id']]['options'] = $value['options'];
$data[$value['id']]['moderate'] = $value['moderate'];
$data[$value['id']]['user_maxsend'] = $value['user_maxsend'];
$data[$value['id']]['way_block'] = $value['way_block'];
$data[$value['id']]['auctions'] = $value['auctions'];
$data[$value['id']]['comment'] = $value['comment'];
$data[$value['id']]['filters'] = $value['filters'];
$data[$value['id']]['last_edit'] = $value['last_edit'];
$data[$value['id']]['send_time'] = $value['send_time'];
$data[$value['id']]['unsubs'] = $value['unsubs'];
$data[$value['id']]['subscribers'] = $value['subscribers'];
$data[$value['id']]['sended_wrong'] = $value['sended_wrong'];
$data[$value['id']]['subsid'] = $value['subsid'];
$data[$value['id']]['loop_send'] = $value['loop_send'];
$data[$value['id']]['loop_finish'] = $value['loop_finish'];
}
return $data;

} else return false;

}

function os($where='', $order='id', $limit=0) {
global $db;
if (!$order) $order='id';
if ($limit) $limits = "limit ".$limit."";
$sql = "SELECT * FROM os WHERE 1 ".$where." ORDER by ".$order." DESC ".$limits."";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['mobile'] = $value['mobile'];
$data[$value['id']]['key'] = $value['key'];
$data[$value['id']]['agent'] = $value['agent'];
}
return $data;

} else return false;

}

function browsers($where='', $order='id') {
global $db;
if (!$order) $order='id';
$sql = "SELECT * FROM browsers WHERE 1 ".$where." ORDER by ".$order." DESC";

$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
if (is_array($info)) {
$data = array();
foreach ($info as $row => $value) {
$data[$value['id']]['name'] = $value['name'];
$data[$value['id']]['mobile'] = $value['mobile'];
$data[$value['id']]['key'] = $value['key'];
}
return $data;

} else return false;

}