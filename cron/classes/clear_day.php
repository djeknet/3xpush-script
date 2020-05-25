<?php



           
$db->sql_query('UPDATE traf_exchange SET today_send = 0');    
$db->sql_query('UPDATE traf_exchange_admins SET sended_today = 0, `lock`=0');  
      
$result = "clear day stat";  