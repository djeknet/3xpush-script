<?php

require_once 'Model.php';

class LandingsModel extends Model
{
    public function getData($macros)
    {
        global $db;

        $sql = sprintf('select id  from landings where html like "%%[%s]%%" limit 2;', text_filter($macros));
        $items = $db->sql_fetchrowset($db->sql_query($sql));

        $data = array();

        if (is_array($items)) {
            foreach ($items as $item) {
                $data[] = $item['id'];
            }
        }

        return $data;
    }

    public function findMacros(&$macrosItems = [])
    {
        $data = [];

        foreach ($macrosItems as $key => $macrosItem) {
            $data[$key] = $this->getData($key);
        }
        return $data;
    }

}
