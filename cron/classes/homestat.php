<?php

$stat=array();
$stat['all_users'] = get_onerow('COUNT(id)', 'admins');
$stat['all_subs'] = get_onerow('COUNT(id)', 'subscribers', '1');
if ($stat['all_subs']>0) $stat['all_subs'] = round($stat['all_subs'], 0);
$stat['today_subs'] = get_onerow('SUM(subscribers)', 'daystat', 'date=CURRENT_DATE()');
$stat['today_sended'] = get_onerow('SUM(sended)', 'daystat', 'date=CURRENT_DATE()');

$stat = json_encode($stat);
$db->sql_query("INSERT INTO home_stat (name, value)
        VALUES ('general',  '" . $stat . "')
         ON DUPLICATE KEY UPDATE value='".$stat."'");// or die ("<center><br>".mysqli_error()."</center>");
         
$result = "home stat: ".$stat;