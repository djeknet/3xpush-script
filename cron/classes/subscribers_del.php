<?php


// проверка подписчиков на активность, если активности нет, то помечаем юзера как удаленного
           
$subs = subscribers("AND sended > 500 AND views=0 AND del=0");
if (is_array($subs)) {
     foreach ($subs as $key => $value) {
        $db->sql_query("UPDATE subscribers SET del = 1 WHERE id=".$value['id']."");
        $stat['set_del']++;
     }
     
 $result = "subscribers del: ".json_encode($stat)."";      
}