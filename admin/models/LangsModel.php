<?php

require_once 'Model.php';

class LangsModel extends Model
{
    public function check_macros($name)
    {
        global $db;

        $sql = sprintf('select * from macros where name="%s" limit 1;', text_filter($name));
        $result = $db->sql_fetchrowassoc($db->sql_query($sql));
        return $result;
    }

    public function getMacrosTotal()
    {
        global $db;
        $sql = sprintf('select count(DISTINCT name) as count from macros;');
        $item = $db->sql_fetchrowassoc($db->sql_query($sql));
        if ($item) {
            return $item['count'];
        } else {
            return 0;
        }
    }

    private static function getIds($items) {
        $data = [];
        if(!empty($items)) {
            foreach ($items as $item) {
                $data[$item['id']] = $item['name'];
            }
        }
        return $data;
    }

    public function getMacrosNames($page = 1, $filenum = 5)
    {
        $offset = ($page - 1) * $filenum;

        $limit = sprintf('limit %s, %s', $offset, $filenum);
        $sql = sprintf('select id, name from macros order by created desc %s;', $limit);

        global $db;
        $items = $db->sql_fetchrowset($db->sql_query($sql));

        $data = LangsModel::getIds($items);

        return $data;
    }

    public function getMacros($macros = array())
    {
        global $db;


        $where = '';

        if (!empty($macros)) {
            $ids = array_keys($macros);
            $ids = implode(',', array_map(array($db, 'quote'), $ids));
            $where .= sprintf("and m.id in (%s)", $ids);
        }

        $sql = sprintf("select * from macros m inner join macros_langs ml on m.id=ml.macros_id where 1=1 %s order by m.id desc;", $where);
        $items = $db->sql_fetchrowset($db->sql_query($sql));

        $data = array();

        if (is_array($macros)) {
            foreach ($macros as $macrosId => $macros_name) {

                $data[$macros_name] = [];

                if (!empty($items)) {
                    foreach ($items as $item) {
                        if ($item['macros_id'] == $macrosId) {
                            $data[$macros_name][] = [
                                'lang' => $item['lang'],
                                'text' => $item['text']
                            ];
                        }
                    }
                }
            }
        }

        return $data;

    }

    public function getText($name = '')
    {
        $lang = $this->getLang();
        $data = $this->getDataByKeys($name, $lang);

        if (!$data) {
            $data = $this->getDataByKeys($name, 'en');
        }

        if ($data && isset($data['text'])) {
            return $data['text'];
        } else {
            return '';
        }
    }

    private function getDataByKeys($name, $lang)
    {
        global $db;
        $sql = sprintf("SELECT * FROM macros m inner join macros_langs ml on m.id=ml.macros_id WHERE m.name='%s' and ml.lang='%s' limit 1;", text_filter($name), text_filter($lang));
        $db->sql_query($sql);
        $data = $db->sql_fetchrowassoc();
        return $data;
    }

    public function getIdByName($name)
    {
        global $db;

        $sql = sprintf('select id from macros where name="%s" limit 1;', text_filter($name));
        $item = $db->sql_fetchrowassoc($db->sql_query($sql));

        if ($item) {
            return $item['id'];
        } else {
            return 0;
        }
    }

    public function deleteByName($name)
    {
        global $db;

        $macrosId = $this->getIdByName($name);
        $sql = sprintf('delete from macros_langs where macros_id="%s";', intval($macrosId));
        $db->sql_query($sql);
    }

    private function saveMacros($name)
    {
        global $db;

        $id = $this->getIdByName($name);

        if (!$id) {
            $sql = sprintf("INSERT INTO macros (id, name) VALUES (null, '%s')", text_filter($name));
            $db->sql_query($sql);
            return $db->sql_nextid();
        } else {
            return $id;
        }
    }

    public function save($data)
    {
        global $db;

        if (empty($data['name']) || empty($data['lang']) || empty($data['text'])) {
            return false;
        }

        $macrosId = $this->saveMacros($data['name']);

        $data['text'] = $db->mysqli_real_escape_string($data['text']);

        $sql = sprintf("INSERT INTO macros_langs (id, macros_id, lang, text) VALUES (null, '%s', '%s', '%s')", intval($macrosId), text_filter($data['lang']), text_filter($data['text'], 2));
        $db->sql_query($sql);
    }

    public function replaceMacros(&$html)
    {
        $pattern = '/\[(.+?)\]/';
        preg_match_all($pattern, $html, $matches);

        if (!empty($matches)) {
            foreach ($matches[0] as $key => $match) {
                $value = $this->getText($matches[1][$key]);
                $html = str_replace($match, $value, $html);
            }
        }
    }

    public function search($macros = '', $text = '')
    {
        if ($macros) {
            return $this->search_macros($macros);
        }

        if ($text) {
            return $this->search_text($text);
        }
    }

    private function search_macros($macros)
    {
        global $db;

        $sql = 'select * from macros where `name` like "%' . text_filter($macros) . '%" order by id desc';
        $items = $db->sql_fetchrowset($db->sql_query($sql));

        return LangsModel::getIds($items);
    }

    private function search_text($text)
    {
        global $db;

        $sql = 'select m.id, m.name from macros m inner join macros_langs ml on m.id=ml.macros_id where `ml`.`text` like "%' . text_filter($text) . '%" group by m.id order by m.id desc';
        $items = $db->sql_fetchrowset($db->sql_query($sql));

        return LangsModel::getIds($items);


    }

}
