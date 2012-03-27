<?php

if (!defined('IN_MANA')) {
    exit('Access Denied');
}

class database {

    private $version = '';
    public $querynum = 0;
    private $link;
    /**
     * @var string 数据库语句
     */
    private $_sql;
    function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE) {

        $func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
        if (!$this->link = @$func($dbhost, $dbuser, $dbpw)) {
            $halt && $this->halt('Can not connect to MySQL server');
        } else {
            if ($this->version() > '4.1') {
                global $charset, $dbcharset;
                $dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
                $serverset = $dbcharset ? 'character_set_connection=' . $dbcharset . ', character_set_results=' . $dbcharset . ', character_set_client=binary' : '';
                $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',') . 'sql_mode=\'\'') : '';
                $serverset && mysql_query("SET $serverset", $this->link);
            }
            $dbname && @mysql_select_db($dbname, $this->link);
        }
    }
   
    function select_db($dbname) {
        return mysql_select_db($dbname, $this->link);
    }

    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    function fetch_first($sql) {
        return $this->fetch_array($this->query($sql));
    }

    function result_first($sql) {
        return $this->result($this->query($sql), 0);
    }

    function query($sql, $type = '') {
        global $debug, $discuz_starttime, $sqldebug, $sqlspenttimes;

        $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
                'mysql_unbuffered_query' : 'mysql_query';
        if (!($query = $func($sql, $this->link))) {
            if (in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
                $this->close();
                require API_ROOT . 'config.inc.php';
                $this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
                $this->query($sql, 'RETRY' . $type);
            } elseif ($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                $this->halt('MySQL Query Error', $sql);
            }
        }

        $this->querynum++;
        return $query;
    }

    function affected_rows() {
        return mysql_affected_rows($this->link);
    }

    function error() {
        return (($this->link) ? mysql_error($this->link) : mysql_error());
    }

    function errno() {
        return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
    }

    function result($query, $row) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function num_fields($query) {
        return mysql_num_fields($query);
    }

    function free_result($query) {
        return mysql_free_result($query);
    }

    function insert_id() {
        return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }

    function fetch_row($query) {
        $query = mysql_fetch_row($query);
        return $query;
    }

    function fetch_fields($query) {
        return mysql_fetch_field($query);
    }

    function version() {
        if (empty($this->version)) {
            $this->version = mysql_get_server_info($this->link);
        }
        return $this->version;
    }

    function close() {
        return mysql_close($this->link);
    }

    function halt($message = '', $sql = '') {
        echo 'SQL Error:<br />' . $message . '<br />' . $sql;
    }

    # expend functions  by iwege

    /**
     * 设置数据库语句
     *
     * @param string $sql
     * @return none
     */
    function setSql($sql) {
        $this->_sql = $sql;
        return;
    }

    /**
     * 获取数据库语句
     * @return string
     */
    function getSql() {
        return $this->_sql;
    }

    /**
     * 返回设置前缀之后的表名称
     *
     * @param string $table
     * @return string
     */
    function setPre($table) {
		global $prefix;
        return $prefix.$table;
    }

    /**
     * 获取单一结果
     * @return string
     */
    function loadResult() {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $ret = null;
        if ($row = mysql_fetch_row($cur)) {
            $ret = $row[0];
        }
        mysql_free_result($cur);
        return $ret;
    }

    /**
     * 获取结果的数组
     * @param int $numinarray  返回哪行数据。
     * @return array
     */
    function loadResultArray($numinarray = 0) {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_row($cur)) {
            $array[] = $row[$numinarray];
        }
        mysql_free_result($cur);
        return $array;
    }

    /**
     * 返回匹配的第一个数组
     * @return array
     */
    function loadAssoc() {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $ret = null;
        if ($array = mysql_fetch_assoc($cur)) {
            $ret = $array;
        }
        mysql_free_result($cur);
        return $ret;
    }

    /**
     * 根据key返回多维数组列
     * @param string $key
     * @return array
     */
    function loadAssocList($key='') {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_assoc($cur)) {
            if ($key) {
                $array[$row[$key]] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysql_free_result($cur);
        return $array;
    }

    /**
     * 返回查询的第一行
     * @return array
     */
    function loadRow() {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $ret = null;
        if ($row = mysql_fetch_row($cur)) {
            $ret = $row;
        }
        mysql_free_result($cur);
        return $ret;
    }

    /**
     * get row list
     * @param string/int $key 主字段的名称
     * @return array
     */
    function loadRowList($key=null) {
        if (!($cur = $this->query($this->_sql))) {
            return null;
        }
        $array = array();
        while ($row = mysql_fetch_row($cur)) {
            if ($key !== null) {
                $array[$row[$key]] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysql_free_result($cur);
        return $array;
    }

}
?>
