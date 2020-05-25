<?php

define("DB", true);
define('END_TRANSACTION', 'END_TRANSACTION');  

require_once("config.php");


class sql_db
{
    var $db_connect_id;
    var $query_result;
    var $row = array();
    var $rowset = array();
    var $num_queries = 0;
    var $total_time_db = 0;
    var $time_query = "";

    function __construct($sqlserver, $sqluser, $sqlpassword, $database)
    {

        $this->db_connect_id = mysqli_connect($sqlserver, $sqluser, $sqlpassword, $database);
        mysqli_query($this->db_connect_id, "SET NAMES utf8mb4");

        if ($this->db_connect_id) {
            return $this->db_connect_id;
        } else {
            return false;
        }
    }

    function get_conid()
    {
        static $conid = 0;
        if ($conid == 0) $conid = sql_db();
        return $conid;
    }

    function mysqli_result($result, $number, $field = 0)
    {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }

    function sql_close()
    {
        if ($this->db_connect_id) {
            if ($this->query_result) @mysqli_free_result($this->query_result);
            $result = @mysqli_close($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_query($query = "", $transaction = false)
    {
        unset($this->query_result);
        if ($query != "") {
            $tdba = explode(" ", microtime());
            $tdba = $tdba[1] + $tdba[0];
            $this->query_result = @mysqli_query($this->db_connect_id, $query);
            $tdbe = explode(" ", microtime());
            $tdbe = $tdbe[1] + $tdbe[0];
            $total_tdb = ($tdbe - $tdba);
            $this->total_time_db += $total_tdb;
            $this->time_query .= "" . substr($total_tdb, 0, 10) > 0.01 . "" ? "<font color=\"red\"><b>" . substr($total_tdb, 0, 10) . "</b></font> s. - [" . $query . "]<br /><br />" : "<font color=\"green\"><b>" . substr($total_tdb, 0, 10) . "</b></font> s. - [" . $query . "]<br /><br />";
        }
        if ($this->query_result) {
            $this->num_queries += 1;
            //unset($this->row[$this->query_result]);
            //unset($this->rowset[$this->query_result]);
            return $this->query_result;
        } else {
            return ($transaction == END_TRANSACTION) ? true : false;
        }
    }

    function sql_numrows($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_num_rows($query_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_affectedrows()
    {
        if ($this->db_connect_id) {
            $result = @mysqli_affected_rows($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_numfields($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_num_fields($query_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fieldname($offset, $query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_field_name($query_id, $offset);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fieldtype($offset, $query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_field_type($query_id, $offset);
            return $result;
        } else {
            return false;
        }
    }

    public function sql_fetchrowassoc($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_fetch_assoc($query_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fetchrow($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_fetch_array($query_id, MYSQLI_NUM);
            return $result;
        } else {
            return false;
        }
    }

    function sql_fetchrowset($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            //unset($row);
            while ($row = mysqli_fetch_array($query_id, MYSQLI_ASSOC)) {
                $result[] = $row;
            }

            return $result;
        } else {
            return false;
        }
    }

    function sql_fetchfield($field, $rownum = -1, $query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            if ($rownum > -1) {
                $result = @mysqli_result($query_id, $rownum, $field);
            } else {
                if (empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
                    if ($this->sql_fetchrow()) {
                        $result = $this->row[$query_id][$field];
                    }
                } else {
                    if ($this->rowset[$query_id]) {
                        $result = $this->rowset[$query_id][0][$field];
                    } else if ($this->row[$query_id]) {
                        $result = $this->row[$query_id][$field];
                    }
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    function sql_rowseek($rownum, $query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            $result = @mysqli_data_seek($query_id, $rownum);
            return $result;
        } else {
            return false;
        }
    }

    function sql_nextid()
    {
        if ($this->db_connect_id) {
            $result = @mysqli_insert_id($this->db_connect_id);
            return $result;
        } else {
            return false;
        }
    }

    function sql_freeresult($query_id = 0)
    {
        if (!$query_id) $query_id = $this->query_result;
        if ($query_id) {
            unset($this->row[$query_id]);
            unset($this->rowset[$query_id]);
            @mysqli_free_result($query_id);
            return true;
        } else {
            return false;
        }
    }

    function sql_error($query_id = 0)
    {
        $result["message"] = @mysqli_connect_error($this->db_connect_id);
        $result["error"] = @mysqli_error($this->db_connect_id);
        $result["code"] = @mysqli_connect_errno($this->db_connect_id);
        return $result;
    }

    function quote($str)
    {
        return "'" . $str . "'";
    }

    public function mysqli_real_escape_string($str)
    {
        return mysqli_real_escape_string($this->db_connect_id, $str);
    }
}


$db = new sql_db($config['master_host'], $config['master_user'], $config['master_pass'], $config['master_db']);

if (!$db->db_connect_id) {
    echo "DB master connect error";
}


?>
