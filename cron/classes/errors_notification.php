<?php
// крон оповещения админов об ошибках в системе
        $last_id = temp_table("last_journal_id");
		$journal = journal("AND error=1 AND id > ".$last_id["last_journal_id"]."");
        
        $admins = admins("AND root=1 AND status=1 AND role=1");
        $result=1;

        if (is_array($journal)) {
        $totalNum=0;
		foreach ($journal as $key => $value) {
		       $text .= "ERROR! User id: ".$value['admin_id'].", Page: ".$value['page'].", Action: ".$value['action']."<br>";

         $totalNum++; 
        if ($totalNum==1) $last_id = $key;
		}
        
     	if ($totalNum>0) {
        foreach ($admins as $id => $arr) {
         alert($text, $id, 'warning');
         newmail($id, $arr['email'], "System errors", $text, 'ru');
       }

			$db->sql_query('UPDATE temp_table SET value = '.$last_id.' WHERE name="last_journal_id"');
		}
        
        $result = "error notification: $totalNum sended";
        } 
