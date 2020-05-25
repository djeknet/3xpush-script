<?php
// сохранение системной статистики за день
  
        $sites = sites();
        $admins = admins();
        $balancelist = balancelist();
        $stat=array();
        $today = date("Y-m-d");
        $journal = journal("AND date >= '$today'");
        $payments = payments("AND create_time >= '$today'");
 
 list($myads_all) = $db->sql_fetchrow($db->sql_query("SELECT COUNT(id) FROM myads"));   
 list($myads_new) = $db->sql_fetchrow($db->sql_query("SELECT COUNT(id) FROM myads WHERE date=CURRENT_DATE()"));        
 list($summa) = $db->sql_fetchrow($db->sql_query("SELECT SUM(summa) FROM balance"));     
 list($balance_accounts) = $db->sql_fetchrow($db->sql_query("SELECT COUNT(*) FROM balance WHERE summa > 0"));   
 list($active_users) = $db->sql_fetchrow($db->sql_query("SELECT COUNT(id) FROM admins WHERE last_login >= '$today'"));  
 list($wm_money) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE date = '$today'"));    
 list($adv_money) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM feed_stat WHERE date = '$today'"));    
  
 list($wm_money_week) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM daystat WHERE date = DATE_ADD(CURRENT_DATE(), INTERVAL -7 DAY)"));  
 list($adv_money_week) = $db->sql_fetchrow($db->sql_query("SELECT SUM(money) FROM feed_stat WHERE date = DATE_ADD(CURRENT_DATE(), INTERVAL -7 DAY)"));       

 if (!$wm_money) $wm_money = 0;
 
 // общая стата  
 $stat[0]['myads_all'] = $myads_all; // всего рассылок
 $stat[0]['myads_new'] = $myads_new; // новых рассылок
 $stat[0]['sites_all'] = count($sites); // всего сайтов
 $stat[0]['users_all'] = count($admins); // всего пользователей
 $stat[0]['balance'] = $summa; // общий баланс
 $stat[0]['active_users'] = $active_users; // активных пользователей
 $stat[0]['account_actions'] = count($journal); // действий в аккаутах
 $stat[0]['balance_accounts'] = $balance_accounts; // аккаунтов с балансом
 $stat[0]['wm_money'] = $wm_money; // заработок вебмастеров
 $stat[0]['wm_money_weekago'] = $wm_money_week; // заработок вебмастеров неделю назад
 
  // статистика по журналам
 if (is_array($journal)) {
    $type=2;
    $stat[$type]['journal_all'] = count($journal); // всего действий
    $wm_pages = array('faq','account','refs','admins','my_send', 'traf_exchange', 'landstat', 'browser', 'sites', 'landinghtml', 'daystat', 'subid', 'feedstat', 'regionstat', 'groupstat', 'subscribers', 'sended', 'clicks');

     foreach ($journal as $key => $value) {
         foreach ($wm_pages as $key2 => $value2) {
         if (stripos($value['page'], $value2) != false) {
          $stat[$type][$value2]++; 
          break;
          }
         }

        } 
        }
        
  // статистика по сайтам
 if (is_array($sites)) {
     $type=3;
     $stat[$type]['sites_all'] = count($sites); // всего сайтов
    foreach ($sites as $key => $value) {
        $days = DateDiffInterval($value['date'], '', 'D');
        $days = round($days, 0);
        
        if ($value['date']==$today) {
          $stat[$type]['sites_new']++;  // добавлено  
        }
        if ($value['type']==1) {
          $stat[$type]['sites']++;  // сайтов  
        }
        if ($value['type']==2) {
          $stat[$type]['landings']++; // лендингов   
        }
        if (stripos($value['last_subscribe'], $today) != false) {
           $stat[$type]['active']++; // активных сайтов  
           if ($days >= 7) {
           $stat[$type]['active_7days']++;  // активных больше 7 дней
           } elseif ($days >= 30) {
           $stat[$type]['active_30days']++; // активных больше 30 дней  
           } elseif ($days >= 60) {
           $stat[$type]['active_60days']++; // активных больше 60 дней  
           } elseif ($days >= 90) {
           $stat[$type]['active_90days']++; // активных больше 90 дней  
           } elseif ($days >= 120) {
           $stat[$type]['active_120days']++; // активных больше 120 дней   
           } elseif ($days >= 200) {
           $stat[$type]['active_200days']++; // активных больше 200 дней  
           }         
        }
        
    }
    
    }
    
  // статистика по админам
 if (is_array($admins)) {
     $type=4;
     $stat[$type]['users_all'] = count($admins); // всего пользователей
    foreach ($admins as $key => $value) {
        
        if (stripos($value['last_edit'], $today) != false) {   
        if ($value['status']!=1) {
          $stat[$type]['blocked']++;  // заблокированные 
        }
        }
        if ($value['telegram']) {
          $stat[$type]['online_contact']++;  // онлайн контакты 
        }
        if ($value['get_mail']!=1) {
          $stat[$type]['get_mail_off']++;  // запретили email  
        }
        if ($value['promo_mail']!=1) {
          $stat[$type]['promo_mail_off']++; // запретили промо email  
        }
        if ($value['auto_money']==1) {
          $stat[$type]['auto_money']++; // включены автовыплаты    
        }
         if ($value['date']==$today) {
          $stat[$type]['new']++;      // новых юзеров
           if ($value['owner_id']!=0) {
          $stat[$type]['guests']++;  // из них гостевых 
        }  
        }
         if (stripos($value['last_login'], $today) !== false) {
          $stat[$type]['active']++;  // активные аккаунты  
        } 
    }
    
    }
  
    foreach ($stat as $type => $value) {
        $value = json_encode($value);
        $db->sql_query("INSERT INTO sysstat (id, date, type, data)
        VALUES (NULL, CURRENT_DATE(), '" . $type . "', '" . $value . "')  
        ON DUPLICATE KEY UPDATE data='".$value."'");
    }

    
$result = "sysstat: ".json_encode($stat)."";