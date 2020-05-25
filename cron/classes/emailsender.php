<?php

		$sql = 'SELECT * FROM mails WHERE status = 0 AND date < now() ORDER by date ASC limit 100';
         $info = $db->sql_query($sql);
         $info = $db->sql_fetchrowset($info);

		$toUpdate = array();
      		// Всего получателей
		$stat=array();

        if (is_array($info)) {
		foreach ($info as $key => $value) {
           if ($value['email'] && $value['title'] && $value['content']) {
            $value['content'] = htmlspecialchars_decode($value['content'], ENT_QUOTES);
            $id = $value['id'];
            $siteurl = $settings['siteurl'];
            $value['content'] = str_replace('a href="', 'a href="https://'.$siteurl.'/mail.php?id='.$id.'&type=1&url=', $value['content']);
            
			 $sended =	mail_send($value['email'], $settings['from_mail'], $value['title'], $value['content'], $value['admin_id'], $value['lang'], '', 0, $value['id']);
			if ($sended==true) {
            $stat['sended']++;

			$toUpdate[] = $value['id'];
            sleep(1);
            } else {
            $stat['sended_error']++;    
            }
            }
		}

		if (!empty($toUpdate)) {
			$db->sql_query('UPDATE mails SET status = 1 WHERE id IN(' . implode(',', $toUpdate) . ')');
            
            $db->sql_query('INSERT INTO mails_stat (date, sended) VALUES (CURRENT_DATE(), '.$stat['sended'].') ON DUPLICATE KEY UPDATE  sended = sended + '.$stat['sended'].'');
		}
        
        if ($stat['sended_error']) {
            $db->sql_query('INSERT INTO mails_stat (date, error_send) VALUES (CURRENT_DATE(), '.$stat['sended_error'].') ON DUPLICATE KEY UPDATE  error_send = error_send + '.$stat['sended_error'].'');
        }
        
        $result = "email sender: ".json_encode($stat)."";
        } 

