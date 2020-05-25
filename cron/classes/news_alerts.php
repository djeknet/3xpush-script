<?php
// newsletter alerts to users
        $news = news("AND alert_sended=0 AND send_alert=1 AND date <= CURRENT_DATE()");
        $stat=array();
        if (is_array($news)) {
        $admins = admins("AND status=1"); 
        $settings_all = settings_all();  
        $admins_lang=array();
        foreach ($settings_all as $key => $value) {
             if ($value['name']=='lang') {
              $admins_lang[$value['admin_id']] = $value['value'];  
             }
        }    
        $totalNum=0;
		foreach ($news as $key => $value) {
		  $titles = json_decode($value['title'], true);
          $content = json_decode($value['content'], true);
                   
		       foreach ($admins as $id => $arr) {
		          $lang = $admins_lang[$id];
                  if (!$lang) $lang = 'en';
                  
                   $content_mail = htmlspecialchars_decode($content[$lang], ENT_QUOTES);
                   $content_alert = text_filter($content_mail);

		           $alert_text = "<b>".$titles[$lang]."</b>. ".$content_alert;
               
               
               alert($alert_text, $id, 'info');
               if ($arr['get_mail']==1) {
               newmail($id, $arr['email'], $titles[$lang], $content_mail, $lang);
               $stat['mail_sended']++;
               }
               $stat['alert_sended']++;
               }

         	$db->sql_query('UPDATE news SET alert_sended = 1 WHERE id='.$key.'');
		}
        
		
        
        $result = "news alerts: ".json_encode($stat)."";
        }