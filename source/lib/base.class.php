<?php if (!defined("API_ROOT")) {exit('no right');}

/**
 * 基础类
 */
class base {

    protected $db;
    protected $_table;
    protected $_keyid;
    protected $_log;

    public function __construct($db) {
        global $prefix, $db;
        $this->db = $db;
        $this->_table = $prefix . $this->_table;
        $this->_log = $prefix . $this->_log;
    }

    /**
     * get the data from database
     * @param string $where
     * @param int $num how many result return
     * @param int $from what num result from
     * @return array
     */
    public function select($where = '', $from = NULL, $num = 0, $order = NULL) {
        $_limit = '';

        if ($where) {
            $where = ' WHERE ' . $where . ' ';
        }
        if ($num) {
            if ($from) {
                $_row_num = $from . ',' . $num;
            } else {
                $_row_num = $num;
            }
            $_limit = "LIMIT " . $_row_num;
        }

        $this->db->setSql("SELECT * FROM `{$this->_table}`  $where  $order  $_limit ");
        return $this->db->loadAssocList();
    }

    /**
     * insert  a data to database
     * @param array $arr
     * @return int
     */
    public function insert($arr) {
        $v = sqlString($arr);
        $sql = "INSERT INTO `{$this->_table}` SET $v";
        $this->db->query($sql);
        return $this->db->insert_id();
    }

    /**
     * update data
     * @param array $arr
     * @param string $where
     * @return int
     */
    public function update($arr, $where) {
        $v = sqlString($arr);
		
        $this->db->query("UPDATE `{$this->_table}` SET $v $where");
        return $this->db->affected_rows();
    }

    /**
     * edit one data with the key id
     * @param <type> $arr
     * @param <type> $id
     * @return <type>
     */
    public function editWithKey($arr, $id) {
        $where = ' WHERE `' . $this->_keyid . "` = '" . $id . "'";

        return $this->update($arr, $where);
    }

    /**
     * insert one data
     * @param array $arr
     * @return int
     */
    public function add($arr) {
        return $this->insert($arr);
    }

    /**
     * get one data from mysql with key id
     * @param int $id
     * @return array
     */
    public function getOneWithKey($id, $addon = NULL) {
        $where = '`'.$this->_keyid . '` = "' . $id.'"';
		if ($addon ) {
			$where .= ' AND '.$addon;
		}
        $result = $this->select($where);
	
        return isset($result[0])?$result[0]:false;
    }

    /**
     * get all data from mysql;
     * @param int $num
     * @param int $from
     * @return array
     */
    public function getAll($where = '', $from = NULL, $num = 0, $order = NULL) {
        return $this->select($where, $from, $num, $order);
    }

    /**
     * 删
     * @param int $id
     */
    public function del($id, $upid) {
        if ($upid) {
            $this->editWithUpid(array('del' => '1'), $upid);
        }
        return $this->editWithKey(array('del' => '1'), $id);
    }

	public function removeWithKey($id){
		$where = $this->_keyid.' = "'.$id.'"';
		$this->db->query('DELETE FROM '.$this->_table.' WHERE '.$where);
		return $this->db->affected_rows();
	}

    /**
     * 根据上级id来增加内容
     * @param <type> $arr
     * @param <type> $upid
     * @return <type>
     */
    public function editWithUpid($arr, $upid) {
        return $this->update($arr, array('upid' => $upid));
    }

    public function getCount($where) {
        if ($where) {
            $where = ' WHERE ' . $where;
        }
        $this->db->setSql("SELECT COUNT(*) FROM {$this->_table} $where");
    
        return $this->db->loadResult();
    }
    public function getTable(){
        return $this->_table;
    }
    public function getKey(){
        return $this->_keyid;
    }
}
?>
