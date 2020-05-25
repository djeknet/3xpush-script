<?php
// обновленние временных данных в temp_table

    
$sql = 'SELECT tag FROM `subscribers` group by tag';
$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
 if (is_array($info)) {
    $i=0;
     foreach ($info as $key => $value) {
     if ($value['tag']) {$targets['tags'][$i] = $value['tag'];
     $i++;}
     }
    } 
    
$sql = 'SELECT city, cc FROM `subscribers` group by city';
$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);
 if (is_array($info)) {

     foreach ($info as $key => $value) {
     if ($value['city']) $targets['citys'][$value['city']] = $value['cc'];
     }
    } 


$i=0;                           
foreach ($targets as $key => $value) {
    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
$value = addslashes($value);
    $db->sql_query("UPDATE temp_table SET value=\"".$value."\" WHERE name='".$key."'");
$i++;
}
            
$result = "temp table: $i";    
