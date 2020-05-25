<?php
// рассылка новостей в группу
// id группы RU
$chat_id = $settings['telegram_chat_id'];
// id группы EN
$chat_id_en = $settings['telegram_chat_id_en'];

//$chat_id  = "-386881773"; // id тест группы

        $news = news("AND chat_sended=0 AND send_chat=1 AND date <= CURRENT_DATE()");
        $stat=array();
        if (is_array($news)) {
   
        $totalNum=0;
		foreach ($news as $key => $value) {
		  $titles = json_decode($value['title'], true);
          $content = json_decode($value['content'], true);
           
           $text =  "<b>".$titles['ru']."</b>\n".$content['ru'];  
           if ($titles['en']) $text_en =  "<b>".$titles['en']."</b>\n".$content['en'];  
                          
             sendTelegram('sendMessage', array(
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $text));
            
            if ($chat_id_en && $text_en) {
              sendTelegram('sendMessage', array(
            'chat_id' => $chat_id_en,
            'parse_mode' => 'HTML',
            'text' => $text_en));  
            }
                 

            $db->sql_query('UPDATE news SET chat_sended = 1 WHERE id='.$key.'');
            $stat['all']++;
		}
        
		
        
        $result = "news chat: ".json_encode($stat)."";
        }