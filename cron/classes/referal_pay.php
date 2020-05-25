<?php
// calculation of the earnings of the referral based on the earnings of his referrals

$referals = referals();
$yesterday = gettime(1);
$today = date("Y-m-d");
$procents = array(0 => 5, 30 => 6, 50 => 7, 70 => 8, 100 => 10);// commission percentage depending on the number of active users
$procent = 5;

if (is_array($referals)) {
    $admins_refs = array();
    foreach ($referals as $key => $value) {
        $active=0;
         if ($value['status']==1) {
            if (stripos($value['balance_edit'], $today) !== false) {
              $active = 1;
             }
             if (stripos($value['balance_edit'], $yesterday) !== false) {
              $active = 1;
             }
         }
          if ($active==1) {
            $admins_refs[$value['owner']]['active'] ++;
          }
           $admins_refs[$value['owner']]['all'] ++;
        }
        $stat=array();
      
    foreach ($referals as $key => $value) {
        list($saved) = $db->sql_fetchrow($db->sql_query("SELECT date FROM refstat WHERE date='".$yesterday."' AND admin_id='".$value['owner']."'"));
        if (!$saved) {
           
         list($money) = $db->sql_fetchrow($db->sql_query("SELECT money FROM daystat WHERE date='$yesterday' AND admin_id='".$value['admin_id']."'"));   
           if ($money > 0) {
              $active_refs = $admins_refs[$value['owner']]['active'];
            
              foreach ($procents as $q => $proc) {
                if ($active_refs >= $q) {
                    $procent = $proc;
                }
              }
              
              $comission = round($money / 100 * $procent, 4);
              
              $save_stat[$value['owner']]['money'] += $comission;
              $save_stat[$value['owner']]['proc'] = $procent; 
              $save_stat[$value['owner']]['active'] = $active_refs; 
              
              $check_code = fff($comission);
              $db->sql_query('UPDATE balance SET summa = summa + '.$comission.', got_money=got_money+'.$comission.', last_edit=now(), last_sum='.$comission.', check_code="'.$check_code.'" WHERE admin_id = '.$value['owner'].'');
              $db->sql_query('UPDATE referals SET money = money + '.$comission.' WHERE owner='.$value['owner'].' AND admin_id = '.$value['admin_id'].'');
              
              $stat['all']++;
              $stat['comission'] += $comission;
           } else {
            $stat['nomoney']++;
           }
        }
        }
        if (is_array($save_stat)) {
            foreach ($save_stat as $key => $value) {
                $all_users = $admins_refs[$key]['all'];
             $db->sql_query('INSERT INTO refstat (date, admin_id, money, proc, active_users, all_users) VALUES ("'.$yesterday.'", "'.$key.'", "'.$value['money'].'", "'.$value['proc'].'", "'.$value['active'].'", "'.$all_users.'")');       
                }
        }
$result = "referal pay: ".json_encode($stat)."";        
}
       
        
   
       
