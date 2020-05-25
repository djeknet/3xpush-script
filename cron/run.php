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



    function get_sec()
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        return $mtime;
    }

    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    ini_set('display_errors', '1');
    set_time_limit(0);
    ini_set('max_execution_time', 0);
    ini_set('set_time_limit', 0);

    require_once('../include/config.php');
    require_once('../include/mysql.php');
    require_once('../include/func.php');
    require_once('../include/info.php');
    require_once('../include/stat.php');


@$view = intval($_GET['view']);
if ($view==1) {
$check_login = check_login();
if ($check_login['root']!=1) exit;
}

    $settings = settings();
    if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}
    $id = intval($_GET['id']);
    if ($id) {
          $query = "
        SELECT *
        FROM crons
        WHERE id=$id";  
    } else {
            $query = "
        SELECT *
        FROM crons
        WHERE crons.location = 'master'
            AND (crons.last_start IS NULL || (TIME_TO_SEC(TIMEDIFF(NOW(), crons.last_start)) >= crons.frequency AND crons.last_end >= crons.last_start))
            AND crons.is_stable = 1
            AND (crons.time_from <= DATE_FORMAT(NOW(),'%H:%i:%s') || crons.time_from IS NULL)
            AND (crons.time_to >= DATE_FORMAT(NOW(),'%H:%i:%s') || crons.time_to IS NULL)";
    }
    


    $stat = $db->sql_query($query);
    $info = $db->sql_fetchrowset($stat);
    if (is_array($info)) {
    foreach ($info as $row) {
        $db->sql_query('UPDATE crons SET last_start = now() WHERE id = '.$row['id'].'');
        $startTime = get_sec();
        $result='';

         include 'classes/' . $row['cronfile'] . '.php';
        
         $execTime = get_sec() - $startTime;
         $db->sql_query('UPDATE crons SET last_end = now(), count = count + 1, time = time + '.$execTime.' WHERE id = '.$row['id'].'');
         if ($view==1) {
         echo "<hr>$result<hr>";
         }
         if (is_array($result)) {
           $result = json_encode($result, JSON_UNESCAPED_UNICODE); 
           $status = 'error';
         }  elseif (!$result) {
           $result = $row['cronfile'].' - no result'; 
           $status = 'error';
         } else {
           $status = 'success'; 
         }
         $result = addslashes ($result);
         $query = '
          INSERT INTO reports (id, type, status, description, how_long, date, ip, cron_id)
          VALUES(NULL, "cron", "'.$status.'", "'.$result.'", "' . $execTime . '", now(), "' . getenv('SERVER_ADDR') . '", '.$row['id'].')';
          $db->sql_query($query);

        }
     } 

