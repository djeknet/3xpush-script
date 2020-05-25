<?php

class Model
{
    private static $_instance;

    public static function getInstance()
    {
        $className = get_called_class();
        if (isset(self::$_instance[$className])) {
            return self::$_instance[$className];
        } else {
            return self::$_instance[$className] = new $className;
        }
    }

    public function getLang()
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return $lang;
    }

}
