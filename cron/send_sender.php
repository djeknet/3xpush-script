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

error_reporting(E_WARNING);
ini_set('display_errors', 1);

$total_time = microtime(1);

set_time_limit(300);


$server_ip = getenv("SERVER_ADDR");

require_once('../include/config.php');
require_once('../include/mysql.php');
require_once('../include/SxGeo.php');
require_once('../include/func.php');
require_once('../include/info.php');

$check_login = check_login();
if ($check_login['root']==1) {
$view =  intval($_GET['view']);
$id =  intval($_GET['id']); // subscriber id
$otl =  intval($_GET['otl']);
$nosend =  intval($_GET['nosend']);
if ($nosend==1) $send = 0; else $send=1;
$sid = intval($_GET['sid']);
 
    }
if ($view==1) {
header('Content-Type: text/html; charset=utf-8');
}

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}


$settings = settings();

date_default_timezone_set($settings['timezone']);

$now = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$admin_sended = array();
if ($settings['enable_messaging']!=1) exit;

if ($memcached) {
$code = md5('filter_ids');
$filter_ids = $memcached->get($code);
if ($filter_ids) {
    $where2 = "AND id NOT IN (".implode(',', $filter_ids).")"; // отфильтровываем объявления, по которым еще производится рассылка от повторов
}
}
 

 if ($id) {
 $where = "id='$id'";
 } else {
 $where .= "AND status=1 AND moderate=1 AND send_time < now() AND ((sended+sended_wrong < subscribers AND last_send < DATE_ADD(NOW(), INTERVAL -24 HOUR) ) OR (loop_send=1 AND loop_finish=0))";
 }

$ads = myads($where);
if (!is_array($ads)) {
echo "no ads<br />";
exit;
}
if ($memcached) {
foreach ($ads as $key => $value) {
    $ids[] = $key;
}
$memcached->set($code, $ids, false, time() + 600);
}
$now = date("Y-m-d H:i:s");

foreach ($ads as $key => $value) {

    
$where='';
$loop_is=0;
// если это рассылка до просмотра, то забираем связи из таблицы, чтобы выбрать только тех подписчиков, кто не увидел эту рассылку
if ($value['loop_send']==1) {
$sql = "SELECT subs_id, last_send FROM send_loop WHERE myads_id=".$key." AND is_view=0 AND sended < 6";
$send_loop = $db->sql_query($sql);
$send_loop = $db->sql_fetchrowset($send_loop); 
if (is_array($send_loop)) {
foreach ($send_loop as $key1 => $value1) {
    
    $hours = round(DateDiffInterval($value1['last_send'], $now), 0);
    
    if ($hours >= 4) {
    $value['subsid'] .= ','.$value1['subs_id'];
    $loop_is=1;
    }
} 
// если нет подписчиков для рассылки на данное время, то пропускаем рассылку
if ($loop_is!=1) {
   continue; 
}
$value['subsid'] = ltrim($value['subsid'], ',');

// если подписчиков нет, то выставляем финиш рассылке и очищаем таблицу
} else {
  $db->sql_query("UPDATE myads SET loop_finish=1 WHERE id='".$key."'");  
  $db->sql_query("DELETE FROM send_loop WHERE myads_id=".$key."");  
  continue;
}
}

// проверяем, есть ли админ в таблице по обмену трафиков
$sql = "SELECT b.id, b.traf_id, b.site_id, b.admin_site, b.max_send  FROM traf_exchange as a 
LEFT JOIN traf_exchange_admins as b ON (a.id=b.traf_id)  WHERE b.admin_id=".$value['admin_id']." AND b.lock=0 AND b.status=1 AND b.sended_today < b.max_send AND a.today_send < a.max_send";
$traf_exchange = $db->sql_query($sql);
$traf_exchange = $db->sql_fetchrowset($traf_exchange); 
if (is_array($traf_exchange)) {
foreach ($traf_exchange as $key1 => $value1) {
    $exch_data[$value1['site_id']]['id'] = $value1['id'];
    $exch_data[$value1['site_id']]['traf_id'] = $value1['traf_id'];
    $exch_data[$value1['site_id']]['admin_site'] = $value1['admin_site'];
    $exch_data[$value1['site_id']]['max_send'] = $value1['max_send'];
    
    $exch_sids[] = $value1['site_id'];
    }
    $exch_sids_str = implode(',', $exch_sids);
}

if ($exch_sids_str) {
    $where = "AND (admin_id=".$value['admin_id']." OR sid IN (".$exch_sids_str.")) ";
} else {
    $where = "AND admin_id=".$value['admin_id']." ";
}
if ($value['regions']) {
    $regions = str_replace(",", "','", $value['regions']);
    $where .= "AND cc IN ('".$regions."') ";
}
if ($value['langs']) {
    $langs = str_replace(",", "','", $value['langs']);
    $where .= "AND lang IN ('".$langs."') ";
}
if ($value['tags']) {
    $tags = str_replace(",", "','", $value['tags']);
    $where .= "AND tag IN ('".$tags."') ";
}
if ($value['sids']) {
    $where .= "AND sid IN (".$value['sids'].") ";
}
if ($value['subsid']) {
    $where = "AND id IN (".$value['subsid'].") ";
}
$value['subsid']='';


$sql = "SELECT tag, cc, admin_id, sender_id, id, sid, token, ip, browser, os, browser_short, device, brand, model, last_send_id, subid, createtime FROM subscribers WHERE del=0 ".$where;
$subs = $db->sql_query($sql);
$subs = $db->sql_fetchrowset($subs);
if (is_array($subs)) $allsubs = count($subs);
if (!$allsubs) {
echo "ad $key: no active subsribers<br />";
continue;
} elseif($view==1) {
  echo "ad $key: $allsubs subs: $where<br />";  
}


$settings = settings("AND admin_id=".$value['admin_id']."");

$count=0;
$ads_count=0;

       foreach ($subs as $num => $r) {
       $count++;
       $ads_count++;
       // делаем паузу после пачки рассылок
       if ($count >= 10000) {
        $count=0;
        sleep(2);
       } 
       
           
    // делаем проверку, если объявление отключили, то останавливаем рассылку
    if ($ads_count >= 1000) {
    $check_status = get_onerow('id', 'myads', "id=$key AND status=1");
    if (!$check_status) break;
    $ads_count=0; 
    }
  // ССЫЛКА ДЛЯ ПЕРЕХОДА
  $rand =  rand(10,9999999999); 
  $datetime2 = date_create($now);
  $datetime1 = date_create($r['createtime']);
  $interval = date_diff($datetime1, $datetime2, true);
  $sub_days = $interval->days;
  
  // если владелец подписчика и объявления не равны, значит это обмен трафиком
  if ($r['admin_id']!=$value['admin_id']) {
    $traf_exchange=1;
    // чекаем сколько сделано рассылок из разрешенных владельцем подписчика
    $exch_is_ok = get_onerow('id', 'traf_exchange', "id=".$exch_data[$r['sid']]['traf_id']." AND today_send < max_send");
    if (!$exch_is_ok) {
       $stat['exchange_limit']++; 
       continue;
    }
    // делаем проверку лимита рассылок для админа, если он превышен, локаем запись для дальнейшей выборки
    $as = $admin_sended[$value['admin_id']][$r['sid']];
    if ($as >= $exch_data[$r['sid']]['max_send']) {
      $db->sql_query("UPDATE traf_exchange_admins SET `lock`=1 WHERE id='".$exch_data[$r['sid']]['id']."'"); 
      if ($value['loop_send']==1) {
        $db->sql_query("DELETE FROM send_loop WHERE subs_id=".$r['id']." AND myads_id=".$key."");  
        }
      continue;                  
    }
    
  } else $traf_exchange=0;
  
  $geoip = geoip_new($r['ip']);
                $code = md5($config['global_secret'].$r['sid']);
                $link = "owner=".$r['admin_id']."&adv_admin=".$value['admin_id']."&sendid=".$message_id."&uid=".$r['id']."&ip=".$r['ip']."&sended=".$now."&tv=".time()."&brid=".$r['browser_id']."&rnd=".$rand."&adv_id=".$key."&advs_id=&sid=".$r['sid']."&te=".$traf_exchange."&adm_sid=".$exch_data[$r['sid']]['admin_site']."&code=".$code."&out=".base64_encode($value['url'])."&subid=".$r['subid']."&d=".$sub_days."";
                $urlparams = encodelink($link, 1);        
          
                 $title = str_replace("[COUNTRY]", $geoip['country'], $value['title']);
                 $title = str_replace("[REGION]", $geoip['region'], $title);
                 $title = str_replace("[CITY]", $geoip['city'], $title);
                 $title = str_replace("[BRAND]", $r['brand'], $title);
                 $title = str_replace("[MODEL]", $r['model'], $title);  
                 $title = str_replace("[TAG]", $r['tag'], $title);         
               
                 $text = str_replace("[COUNTRY]", $geoip['country'], $value['text']);
                 $text = str_replace("[REGION]", $geoip['region'], $text);
                 $text = str_replace("[CITY]", $geoip['city'], $text);
                 $text = str_replace("[BRAND]", $r['brand'], $text);
                 $text = str_replace("[MODEL]", $r['model'], $text);
                 $text = str_replace("[TAG]", $r['tag'], $text);
              
                                      
                    /* NOTIFICATION */
$adsarr['title'] = $title;
$adsarr['body'] = $text;
$adsarr['icon'] = $value['icon'];
$adsarr['url'] = 'https://'.$settings['domain'].'/go.php?p='.$urlparams;
$adsarr['image'] = $value['image'];
if (!empty($value['options'])) {
$options = json_decode($value['options'], true);  
$adsarr['options'] = array('button1' => $options['button1'], 'button2' => $options['button2']);
}
$adsarr['advid'] = $key;
$site['sid'] = $r['sid'];
$site['subid'] = $r['subid'];
$site['subs_id2'] = $r['id'];
$site['params'] = 0;
$site['server_key'] = $r['sender_id'];

            $data =  send_push($adsarr, $site, $r['token'], $send);
            
            $uniq=1;
            if (stripos($r['last_send'], $today) != false) {
            $uniq=0;    
                }
                
             if (preg_match('/NotRegistered/i', $data) == 1) {
             $stat['unsubs']++;
             $db->sql_query("UPDATE subscribers SET del='1', last_update=now() WHERE id='".$r['id']."'");
             
              $db->sql_query("UPDATE sites SET unsubs=unsubs+1 WHERE id='".$r['sid']."'");
              
              $db->sql_query("UPDATE myads SET unsubs=unsubs+1 WHERE id='".$key."'");

              $db->sql_query("INSERT INTO daystat (date, admin_id, sid, subid, unsubs)
                  VALUES (CURRENT_DATE(), " . $r['admin_id'] . ",  " . $r['sid'] . ", '".$r['subid']."', 1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");
  
              $db->sql_query("INSERT INTO region_stat (date,  admin_id, sid, cc, unsubs)
                  VALUES (CURRENT_DATE(), ".$r['admin_id'].", " . $r['sid'] . ", '" . $r['cc'] . "',  1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");
                  
              $db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, unsubs)
                  VALUES (CURRENT_DATE(), '".$r['browser_id']."', ".$r['admin_id'].", " . $r['sid'] . ",  1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");
                  
              $db->sql_query("UPDATE total_stat SET value=value+1 WHERE name='unsubs' AND admin_id='".$r['admin_id']."'"); 
                  
               $db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES (".$r['admin_id'].", 'unsubs',  1) ON DUPLICATE KEY UPDATE value = value + 1");
               
                 if ($r['last_send_id']!=0) {
        	      $db->sql_query("UPDATE send_report SET unsubs='1' WHERE id='".$r['last_send_id']."'");
                  }  
                  
               if ($value['loop_send']==1) {
                 $db->sql_query("DELETE FROM send_loop WHERE subs_id='".$r['id']."' AND myads_id='".$key."'");   
               }   
             }
             
             $data_enc = json_decode($data,1);
                 if ($send==1) {
                    
                    if ($value['loop_send']==1) {
                     $db->sql_query("UPDATE send_loop SET sended=sended+1, last_send=now() WHERE subs_id='".$r['id']."' AND myads_id='".$key."'");   
                        }
                     // если это рассылка по обмену, то отдельно обновляем стату в таблицах   
                     if ($traf_exchange==1) {    
                      $db->sql_query("UPDATE traf_exchange SET today_send=today_send+1 WHERE id='".$exch_data[$r['sid']]['traf_id']."'");  
                      $db->sql_query("UPDATE traf_exchange_admins SET sended=sended+1, sended_today=sended_today+1 WHERE id='".$exch_data[$r['sid']]['id']."'"); 
                      
                      $db->sql_query("INSERT INTO traf_exchange_stat (id, date, site_id, admin_site, sended)
                       VALUES (NULL, CURRENT_DATE(), " . $r['sid'] . ", '".$exch_data[$r['sid']]['admin_site']."', 1)
                       ON DUPLICATE KEY UPDATE
                       sended = sended + 1");
                     }   
                        
                   if ($data_enc['success']==1) {
                    $stat['all_sended']++;

                    $db->sql_query("UPDATE subscribers SET last_send=now(), next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR), sended=sended+1 WHERE id='".$r['id']."'");

                    $db->sql_query("UPDATE myads SET sended=sended+1, last_send=now() WHERE id='".$key."'");
                    
                    $db->sql_query("INSERT INTO daystat (date, admin_id,  sid, subid, sended, uniq_sended)
                  VALUES (CURRENT_DATE(),  ".$r['admin_id'].", " . $r['sid'] . ", '".$r['subid']."', 1, ".$uniq.")
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1, uniq_sended = uniq_sended + ".$uniq."");

                  $db->sql_query("INSERT INTO region_stat (date, admin_id, sid, cc, sended)
                  VALUES (CURRENT_DATE(),  ".$r['admin_id'].", " . $r['sid'] . ", '" . $r['cc'] . "',  1)
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1");
                  
                  $db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, sended, uniq_sended)
                  VALUES (CURRENT_DATE(), '" . $r['browser_id'] . "', ".$r['admin_id'].", '".$r['sid']."', 1, ".$uniq.")
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1, uniq_sended = uniq_sended + ".$uniq."");  
                  
                   $db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES (".$r['admin_id'].", 'sended',  1) ON DUPLICATE KEY UPDATE value = value + 1");
                  
                  $admin_sended[$value['admin_id']][$r['sid']]++;
                  
                         if ($view==1){ 
                        echo "<strong>".$r['id']." - SENDED!</strong><br>";
                    print_r($data_enc);
                    }
                  } else {
                    $db->sql_query("UPDATE myads SET sended_wrong=sended_wrong+1 WHERE id='".$key."'");
                    
                    $db->sql_query("UPDATE subscribers SET comment='".$data."' WHERE id='".$r['id']."'");
                    $stat['sent_wrong']++;
                    if ($view==1){ 
                        echo "<strong>".$r['id']." - NO SUCCESS!</strong><br>";
                    print_r($data_enc);
                    }
                  }
                }
        }
}
        
                 $total_time2 = microtime(1);

                 $alltime = $total_time2 - $total_time;
                 $decode = json_encode($stat);
                  $desc = "push sender sending: ".$decode."";
      $db->sql_query("INSERT INTO reports (id, type, status, description, how_long, date)
                  VALUES (NULL, 'cron', 'done', '$desc', '$alltime', now())");


if ($otl==1) {

echo '<pre>';
print_r($stat);
echo '</pre>';

echo "<hr>ads:";
print_r($adsarr);

}
if ($memcached) {
$memcached->close();
}