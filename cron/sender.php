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
$otl =  intval($_GET['dev']); 
$nosend =  intval($_GET['nosend']);
if ($nosend==1) $send = 0; else $send=1;
$feed_send =  intval($_GET['feed']);
$sid = intval($_GET['sid']);
}

if ($view==1) {
header('Content-Type: text/html; charset=utf-8');
}
$SxGeo = new SxGeo('../include/SxGeoMax.dat');

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}


$settings = settings();

date_default_timezone_set($settings['timezone']);

$now = date("Y-m-d H:i:s");

$mass_mess_count = $settings['mass_mess_count'];
$send_every = $settings['send_every'];
$minstavka = $settings['minstavka'];
if ($settings['enable_messaging']!=1) exit;

if ($memcached) {
$code_hash = md5('last_hash');
$code = md5('push_sql');
$code_sort = md5('push_sort');
$filter_sub = $memcached->get($code);
}
 if ($settings['test']==1 && $settings['test_id']) {
 $id = $settings['test_id'];
 }
 
 if ($sid) {
 $where .= "new=1 AND sid='$sid'";
 } elseif ($id) {
 $where = "id='$id'";
 } else {
 $where .= "new=1 AND uid=0 AND del=0 AND next_send < now() order by last_send ASC limit ".$mass_mess_count."";
 }
 
$feeds = feeds("AND status=1 AND (max_send=0 OR total_sended < max_send)  AND timeout_next < now()"); 
if ($feeds==false) {
echo "no active feeds<br />";
exit;  
}

$sql = "SELECT cc, next_send, admin_id, browser_id, sender_id, tag, timezone, id, sid, token, ip, browser, os, browser_short, lang, device, brand, model, last_send, last_send_id, subid, referer, createtime, last_hash_adv FROM subscribers WHERE ".$where;
$subs = $db->sql_query($sql);
$subs = $db->sql_fetchrowset($subs);
if (count($subs)<1) {
echo "no active subsribers<br />";
if ($view==1) echo $sql;
exit;
}
$subs_update=array();
foreach ($subs as $key => $val) {
$subs_update[] = $val['id'];
}
$stat['limit']=$mass_mess_count;
$all=count($subs_update);
   if ($view == 1) {
   echo "<br />all: $all<br /><b>sql</b>: $sql<br />";
   	echo '<hr>';
   }

if ($memcached) {
$memcached->set($code, $subs_update, false, time() + 300);

$subs_hash = md5(implode(",", $subs_update));
$last_hash = $memcached->get($code_hash);
$memcached->set($code_hash, $subs_hash, false, time() + 300);

if ($subs_hash==$last_hash && !$id) {
$desc = "push sender: iteration hash exit $subs_hash";
      $db->sql_query("INSERT INTO reports (id, type, status, description, how_long, date)
                  VALUES (NULL, 'cron', 'done', '$desc', '0', now())");
                  exit;
}
    }


        $stat['all']=0;
        $stat['all_sended']=0;
        $stat['all_empty']=0;
        $stat['iterations']=0;
        $stat['nosend']=0;
        $today = date("Y-m-d H:i:s");
        $subs_hashes=array();
        
            foreach ($subs as $r) {
                
if ($memcached) {           
$code = md5('subssended1'.$r['id']);
$is_sended = $memcached->get($code);
if ($is_sended && !$id) {
   if ($view==1) echo "".$r['id']." - cache sended: $is_sended<hr>";
   continue; 
}
$memcached->set($code, $today, false, time() + 600);
}

                $next_send=0;
                
                $settings = settings("AND admin_id=".$r['admin_id']."");

               $stat['all']++;
               $sid = $r['sid'];

               $uniq=1;
               $date = date("Y-m-d");
               $last_send = $r['last_send'];
               $last_send_id = $r['last_send_id'];
               $last_hash = $r['last_hash_adv'];
               if (!$r['subid']) $r['subid']=0;
               
               $uniq_hash = md5($r['ip'].$r['browser'].$r['tag']);  
               
               // check if there was already a push for a unique user, if so, skip
               if (in_array($uniq_hash, $subs_hashes)) {
               if ($view==1) echo "".$r['id']." - uniq sended: $uniq_hash<br>";
                 $next_send=1;  
               }

               $startTime = new Datetime($last_send);
               $hours = round(DateDiffInterval($last_send, $now), 0);
               
               $last_date = $startTime->format('Y-m-d');
               
               $datetime2 = date_create($today);
               $datetime1 = date_create($r['createtime']);
               $interval = date_diff($datetime1, $datetime2, true);
               $sub_days = $interval->days;

               if ($last_date==$date) {
               $uniq=0;
               }
               if ($view==1) {
               echo "<hr>id: ".$r['id']."   &nbsp;&nbsp;&nbsp; user IP: <b>".$r['ip']."</b> cc: <b>".$r['cc']."</b> Agent: <b>".$r['browser']."</b> &nbsp;&nbsp; created: ".$r['createtime']." &nbsp;&nbsp; subs days: $sub_days &nbsp;&nbsp; <b>last send:</b> $last_send &nbsp;&nbsp; <b>next send:</b> ".$r['next_send']." (uniq: $uniq - $last_date==$date) (<b>$hours</b> h)<br />
               sid ".$r['sid']." (sub ".$r['subid'].")<br />";
               }

               if ($memcached) {
               $code_user = md5($r['token']);
               $was_sended = $memcached->get($code_user);
               if ($was_sended && !$id) {
               $stat['povtors']++;
                 
                 $date = date_create();
                 date_timestamp_set($date, $was_sended);
                 $was_sended = date_format($date, 'Y-m-d H:i:s') . "\n";
                 if ($view==1)  echo "<br />".$r['id']." - allready sended $was_sended<br />";
                  $next_send=1;  
               }
               }
               
               if ($next_send==1) {
                 $db->sql_query("UPDATE subscribers SET next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR) WHERE id='".$r['id']."'");
                echo "NO SEND: ";
                continue;
               }
               $createtime = new Datetime($r['createtime']);
               $sub_date = $createtime->format('Y-m-d');


            /* NOTIFICATION */
$ads['title'] = "Loading...";
$ads['body'] = "wait please";
$ads['icon'] = "https://".$settings['domain']."/img.php?subsid=".$r['id'];
$ads['url'] = "https://google.com";
$ads['image'] = "";
$site['sid'] = $r['sid'];
$site['subid'] = $r['subid'];
$site['subs_id'] = $r['id'];
$site['params'] = 1;
if ($r['sender_id']) $site['server_key'] =  $r['sender_id'];

$data =  send_push($ads, $site, $r['token'], $send);
                
                $subs_hashes[] = $uniq_hash;

                if ($view==1) echo "<br />Push result ".$data."<hr>";

                if (preg_match('/NotRegistered/i', $data) == 1) {
                  $stat['unsubs']++;
                 if ($view==1) echo "".$r['id']."  <b>unsubs</b>, last_send_id: $last_send_id, last_hash: $last_hash<br />";

                 $db->sql_query("UPDATE subscribers SET del='1', last_update=now() WHERE id='".$r['id']."'");
                 
                 $db->sql_query("UPDATE sites SET unsubs=unsubs+1 WHERE id='".$sid."'");
  
                 $db->sql_query("INSERT INTO daystat (date, admin_id, sid, subid, unsubs)
                  VALUES (CURRENT_DATE(), " . $r['admin_id'] . ",  " . $sid . ", '".$r['subid']."', 1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");

                  $db->sql_query("INSERT INTO region_stat (date,  admin_id, sid, cc, unsubs)
                  VALUES (CURRENT_DATE(), ".$r['admin_id'].", " . $sid . ", '" . $cc . "',  1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");
                  
                  $db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, unsubs)
                  VALUES (CURRENT_DATE(), '".$r['browser_id']."', ".$r['admin_id'].", " . $sid . ",  1)
                  ON DUPLICATE KEY UPDATE
                  unsubs = unsubs + 1");
                    
                 
                  $db->sql_query("UPDATE total_stat SET value=value+1 WHERE name='unsubs' AND admin_id='".$r['admin_id']."'"); 
                  
                  $db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES (".$r['admin_id'].", 'unsubs',  1) ON DUPLICATE KEY UPDATE value = value + 1");
                  
                  if ($last_send_id!=0) {
        	      $db->sql_query("UPDATE send_report SET unsubs='1' WHERE id='".$last_send_id."'");
                  }
                  if ($last_hash) {
                  $db->sql_query("UPDATE advs SET unsubs=unsubs+1 WHERE hash='".$last_hash."'");
                  }
                }

                $data_enc = json_decode($data,1);
                 if ($send==1) {
                   if ($data_enc['success']==1) {
                    $stat['all_sended']++;

                    $db->sql_query("UPDATE subscribers SET last_send=now(), next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR), sended=sended+1 WHERE id='".$r['id']."'");

                    $db->sql_query("INSERT INTO daystat (date, admin_id,  sid, subid, sended, uniq_sended)
                  VALUES (CURRENT_DATE(),  ".$r['admin_id'].", " . $sid . ", '".$r['subid']."', 1, ".$uniq.")
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1, uniq_sended = uniq_sended + ".$uniq."");

                  $db->sql_query("INSERT INTO region_stat (date, admin_id, sid, cc, sended)
                  VALUES (CURRENT_DATE(),  ".$r['admin_id'].", " . $sid . ", '" . $cc . "',  1)
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1");
                  
                  $db->sql_query("INSERT INTO browser_stat (date, browser_id, admin_id, sid, sended, uniq_sended)
                  VALUES (CURRENT_DATE(), '" . $r['browser_id'] . "', ".$r['admin_id'].", '".$sid."', 1, ".$uniq.")
                  ON DUPLICATE KEY UPDATE
                  sended = sended + 1, uniq_sended = uniq_sended + ".$uniq."");  
                  
                   $db->sql_query("INSERT INTO total_stat (admin_id, name, value)
                  VALUES (".$r['admin_id'].", 'sended',  1) ON DUPLICATE KEY UPDATE value = value + 1");

                  } else {
                   $db->sql_query("UPDATE subscribers SET last_update=now(), next_send=DATE_ADD(NOW(), INTERVAL ".$settings['send_every']." HOUR) WHERE id='".$r['id']."'");  
                    if ($view==1){ 
                        echo "<strong>NO SUCCESS!</strong><br>";
                    print_r($data_enc);
                    }
                  }
                }
            }
                 $total_time2 = microtime(1);

                 $alltime = $total_time2 - $total_time;
                 $decode = json_encode($stat);
                  $desc = "push sender new: ".$decode.", subs hash: ".$subs_hash."";
      $db->sql_query("INSERT INTO reports (id, type, status, description, how_long, date)
                  VALUES (NULL, 'cron', 'done', '$desc', '$alltime', now())");


if ($otl==1) {
echo '<pre>';
print_r($stat);
echo '</pre>';
}
if ($memcached) {
$memcached->close();
}