<?php
// автоматически проверяет активных пользователей и активирует им реф систему, и отправляем уведомления

$sql = "SELECT a.id, a.email, a.name   
FROM  `admins` AS a 
LEFT JOIN balance AS b ON ( a.id = b.admin_id ) 
WHERE a.status =1 
AND a.ref_active =0 
AND a.date <= DATE_ADD( NOW( ) , INTERVAL -7 DAY ) 
AND b.summa >0 
AND a.id NOT IN (Select admin_id FROM referals) ";

$stat = array();
$info = $db->sql_query($sql);
$info = $db->sql_fetchrowset($info);


        $result=1;
          if (is_array($info)) {
          foreach ($info as $id => $arr) {
                
                $settings_admin = settings("AND admin_id=$id");
                
                if ($settings_admin['lang']=='ru') {
                    
                    $title = "Вам активирована реф программа";
                    
                   $text = "Привет, ".$arr['name']."!<br> 
                   Мы активировали тебе реф программу, теперь ты можешь приглашать пользователей в систему и получать процент от их расхода!<br><br>
                   Вот твоя реф ссылка: <a href=https://".$settings['siteurl']."/?r=".$arr['id'].">https://".$settings['siteurl']."/?r=".$arr['id']."</a> <br><br>
                   Процент отчислений зависит от кол-ва привлеченных пользователей, подробнее на странице Рефералы - <a href=https://".$settings['siteurl']."/index.php?m=refs>https://".$settings['siteurl']."/index.php?m=refs</a> "; 
                   
                   
                } else {
                    
                    $title = "We have activated the referral program for you";
                     
                    $text = "Hi, ".$arr['name']."!<br> 
                   We have activated your ref program, now you can invite users to the system and get a percentage of their expense!<br><br>
                  Here is your ref link: <a href=https://".$settings['siteurl']."/?r=".$arr['id'].">https://".$settings['siteurl']."/?r=".$arr['id']."</a> <br><br>
                   The percentage of deductions depends on the number of invited users, more on the page Referrals - <a href=https://".$settings['siteurl']."/index.php?m=refs>https://".$settings['siteurl']."/index.php?m=refs</a> "; 
                   
                }
                
              $db->sql_query("UPDATE admins SET ref_active=1 WHERE id=".$arr['id']."");
             alert($text, $arr['id'], 'info');
             newmail($arr['id'], $arr['email'], $title, $text, $settings_admin['lang']);
             $stat['all']++;

             
                }
                
            $result = "referal activate: ".json_encode($stat)."";
            
          }
