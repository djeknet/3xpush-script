<?php
//check added sites for which there are no subscriptions in a few days

        $sites = sites("AND date <= DATE_ADD( NOW( ) , INTERVAL -3 DAY ) AND subscribers=0 AND partner_api=0 AND type=1");
        
        $admins = admins("AND status=1 AND get_mail=1");
        $settings_all = settings_all();  
        $admins_lang=array();
        foreach ($settings_all as $key => $value) {
             if ($value['name']=='lang') {
              $admins_lang[$value['admin_id']] = $value['value'];  
             }
        }  
        $result=1;
        $stat = array();

        if (is_array($sites)) {

		foreach ($sites as $key => $value) {
		  
		        $hash = md5('newsitest_'.$key);
                $alert_sended = gethash($hash);
                if ($alert_sended!=false) {
                    if ($alert_sended[$hash]['hours'] <= 200) continue; // if less than the specified hours have passed since the last notification, then skip
                }
                $lang = $admins_lang[$value['admin_id']];
                if (!$lang) $lang = 'en';               
                $email = $admins[$value['admin_id']]['email'];
               $name = $admins[$value['admin_id']]['name'];
                
		       if ($lang=='ru') {
                 $title = $name.", ваш сайт не активен";
		         $text = "Привет ".$name."!<br>Вы добавили сайт ".$value['url'].", но у него еще нет подписчиков<br>
                 Может у вас возникли сложности с выбором и установкой кода на сайт? Или есть другие причины? Напишите нам и мы с радостью вам поможем!"; 
		       } else {
		         $title = $name.", your site is inactive";
		         $text = "Hi ".$name."!<br>You added site ".$value['url'].", but it hasn't subscribers yet<br>
                 Maybe you have difficulty choosing and installing the code on the site? Or there are other reasons? Write to us and we will be happy to help you!"; 
		       }
               
             
                if ($email) {
               newmail($value['admin_id'], $email, $title, $text, $lang); 
               sethash($hash);
                $stat['mails']++;   
                }
		}
        
		
        
        $result = "new sites: ".json_encode($stat)."";
         }
