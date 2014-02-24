<?php

/**
 * 基底モデル
 * @author onishi
 */
class Base_model extends CI_Model {

    /**
     * The database table to use, only
     * set if you want to bypass the magic
     *
     * @var string
     */
    protected $_table;

    /**
     * The primary key, by default set to
     * `id`, for use in some functions.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * @var Codeigniter Object
     */
    protected $CI = NULL;
    
    /**
     * $this->dbがmasterに接続しているかどうか
     *
     * @var bool
     */
    var $maintain = TRUE;

    /**
     * The class constructer, tries to guess
     * the table name.
     *
     * @author Jamie Rumbelow
     */
    public function __construct()
    {
        parent::__construct();

        $this->_fetch_table();
        // get class of Codeigniter instance.
        $this->CI =& get_instance();
    }

    /**
     * Start transaction by master-database.
     *
     * @author onishi
     */
    public function trans_begin()
    {
        $this->_get_master_db()->trans_begin();
    }

    /**
     * Rollback transaction by master-database.
     *
     * @author onishi
     */
    public function trans_rollback()
    {
        $this->_get_master_db()->trans_rollback();
    }

    /**
     * Commit transaction by master-database.
     *
     * @author onishi
     */
    public function trans_commit()
    {
        $this->_get_master_db()->trans_commit();
    }

    /**
     * Get a single record by creating a WHERE clause with
     * a value for your primary key
     *
     * @param string $primary_value The value of your primary key
     * @return object
     * @author Phil Sturgeon
     */
    public function get($primary_value)
    {
        return $this->db->where($this->primary_key, $primary_value)
            ->get($this->_table)
            ->row();
    }

    /**
     * Get a single record by creating a WHERE clause with
     * the key of $key and the value of $val.
     *
     * @param string $key The key to search by
     * @param string $val The value of that key
     * @return object
     * @author Phil Sturgeon
     */
    public function get_by()
    {
        $where =& func_get_args();
        $this->_set_where($where);

        return $this->db->get($this->_table)
            ->row();
    }

    /**
     * Similar to get_by(), but returns a result array of
     * many result objects.
     *
     * @param string $key The key to search by
     * @param string $val The value of that key
     * @return array
     * @author Phil Sturgeon
     */
    public function get_many($primary_value)
    {
        $this->db->where($this->primary_key, $primary_value);
        return $this->get_all();
    }

    /**
     * Similar to get_by(), but returns a result array of
     * many result objects.
     *
     * @param string $key The key to search by
     * @param string $val The value of that key
     * @return array
     * @author Phil Sturgeon
     */
    public function get_many_by()
    {
        $where =& func_get_args();
        $this->_set_where($where);

        return $this->get_all();
    }

    /**
     * Get all records in the database
     *
     * @return array
     * @author Phil Sturgeon
     */
    public function get_all() {
        return $this->db->get($this->_table)->result();
    }

    /**
     * Similar to get_by(), but returns a result array of
     * many result objects.
     *
     * @param string $key The key to search by
     * @param string $val The value of that key
     * @return array
     * @author Phil Sturgeon
     */
    public function count_by()
    {
        $where =& func_get_args();
        $this->_set_where($where);

        return $this->db->count_all_results($this->_table);
    }

    /**
     * Delete a row from the database table by the
     * key and value.
     *
     * @param string $sql
     * @param array $args
     * @return array
     * @author Phil Sturgeon
     */
    public function query($sql, $args)
    {
        return $this->db->query($sql, $args)->result();
    }

    /**
     * CRUD a row from the master-database table by query.
     *
     * @param string $sql
     * @param array $args
     * @return bool
     * @author Phil Sturgeon
     */
    public function query_to_master($sql, $args)
    {
        return $this->_get_master_db()->query($sql, $args);
    }

    /**
     * Delete a row from the database table by the
     * key and value.
     *
     * @param string $sql
     * @param array $args
     * @return array
     * @author Phil Sturgeon
     */
    public function query_to_other($sql, $args)
    {
        return $this->db->query($sql, $args);
    }

    /**
     * Insert a new record into the database,
     * calling the before and after create callbacks.
     * Returns the insert ID.
     *
     * @param array $data Information
     * @return integer
     * @author Jamie Rumbelow
     * @modified Dan Horrigan
     */
    public function insert($data)
    {
        $this->_get_master_db()->insert($this->_table, $data);
        return $this->_get_master_db()->insert_id();
    }

    /**
     * Insert a new record into the database,
     * calling the before and after create callbacks.
     * Returns the insert ID.
     *
     * @param array $data Information
     * @return integer
     * @author Jamie Rumbelow
     * @modified Dan Horrigan
     */
    public function insert_batch($data)
    {
        $this->_get_master_db()->insert_batch($this->_table, $data);
        return $this->_get_master_db()->insert_id();
    }

    /**
     * Update a record, specified by an ID.
     *
     * @param integer $id The row's ID
     * @param array $array The data to update
     * @return bool
     * @author Jamie Rumbelow
     */
    public function update($primary_value, $data)
    {
        return $this->_get_master_db()->where($this->primary_key, $primary_value)
            ->set($data)
            ->update($this->_table);
    }

    /**
     * Update a record, specified by $key and $val.
     *
     * @param string $key The key to update with
     * @param string $val The value
     * @param array $array The data to update
     * @return bool
     * @author Jamie Rumbelow
     */
    public function update_by()
    {
        $args =& func_get_args();
        $data = array_pop($args);
        $this->_set_where($args);

        return $this->_get_master_db()->set($data)
            ->update($this->_table);
    }

    /**
     * Delete a row from the database table by the
     * ID.
     *
     * @param integer $id
     * @return bool
     * @author Jamie Rumbelow
     */
    public function delete($id)
    {
        return $this->_get_master_db()->where($this->primary_key, $id)
            ->delete($this->_table);
    }

    /**
     * Delete a row from the database table by the
     * key and value.
     *
     * @param string $key
     * @param string $value
     * @return bool
     * @author Phil Sturgeon
     */
    public function delete_by()
    {
        $where =& func_get_args();
        $this->_set_where($where);

        return $this->_get_master_db()->delete($this->_table);
    }

    /**
     * Retrieve and generate a dropdown-friendly array of the data
     * in the table based on a key and a value.
     *
     * @return array
     */
    function dropdown()
    {
        $args =& func_get_args();

        if(count($args) == 2)
        {
            list($key, $value) = $args;
        }

        else
        {
            $key = $this->primary_key;
            $value = $args[0];
        }

        $query = $this->db->select(array($key, $value))
            ->get($this->_table);

        $options = array();
        foreach ($query->result() as $row)
        {
            $options[$row->{$key}] = $row->{$value};
        }

        return $options;
    }

    /**
    * Fields the result set by the AR properties.
    *
    * @param array $fields
    * @return object	$this
    * @since 1.1.2
    * @author Jamie Rumbelow
    */
    public function field($fields)
    {
        $this->db->select(implode(', ', $fields));
        return $this;
    }

    /**
    * The "set" function.  Allows key/value pairs to be set for inserting or updating
    *
    * @param	mixed
    * @param	string
    * @param	boolean
    * @return	object
    */
    public function set_to_master($key, $value = '', $escape = TRUE)
    {
        $this->_get_master_db()->set($key, $value, $escape);
        return $this;
    }


    /**
    * Orders the result set by the criteria,
    * using the same format as CI's AR library.
    *
    * @param string $criteria The criteria to order by
    * @return object	$this
    * @since 1.1.2
    * @author Jamie Rumbelow
    */
    public function order_by($criteria, $order = 'ASC')
    {
        $this->db->order_by($criteria, $order);
        return $this;
    }

    /**
    * Limits the result set by the integer passed.
    * Pass a second parameter to offset.
    *
    * @param integer $limit The number of rows
    * @param integer $offset The offset
    * @return object	$this
    * @since 1.1.1
    * @author Jamie Rumbelow
    */
    public function limit($limit, $offset = 0)
    {
        $limit =& func_get_args();
        $this->_set_limit($limit);
        return $this;
    }

    /**
     * Creates an Active Record array using a $key as its key.
     *
     * @param string $key Extract key Active Record's
     * @param object $data As an Active Record object
     * @return array Combined array
     * @access public
     */
    public function set_combine($key, $records)
    {

        $result = array();

        if (count($records) == 0)
        {
            return $result;
        }

        foreach ($records as $record)
        {
            $result[$record->$key] = $record;
        }

        return $result;

    }

    /**
     * Fetches the table from the pluralised model name.
     *
     * @return void
     * @author Jamie Rumbelow
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL)
        {
            $this->_table = strtolower(get_class($this));
        }
    }

    /**
     * Sets where depending on the number of parameters
     *
     * @return void
     * @author Phil Sturgeon
     */
    private function _set_where($params)
    {
        if(count($params) == 1)
        {
            $this->db->where($params[0]);
        }
        else
        {
            $this->db->where($params[0], $params[1]);
        }
    }

    /**
     * Sets limit depending on the number of parameters
     *
     * @return void
     * @author Phil Sturgeon
     */
    private function _set_limit($params)
    {
        if (count($params) == 1)
        {
            if (is_array($params[0]))
            {
                $this->db->limit($params[0][0], $params[0][1]);
            }
            else
            {
                $this->db->limit($params[0]);
            }
        }
        else
        {
            $this->db->limit((int) $params[0], (int) $params[1]);
        }
    }

    /**
     * Get object by master-database.
     *
     * @return object	$this->db
     * @author shibuya
     */
    private function _get_master_db() {

        //TODO レプリケーション時はコメントを外すこと
        //$this->load->database(DB_CONNECTION_WRITE);

        //$CI =& get_instance();

        //return $CI->db;
        
        // If is it starting transaction, then return '$this->db'.
        if ($this->maintain == TRUE) {
            return $this->db;
        }

        $this->maintain = TRUE;

        // Load db by master
        // Notice: '$this->db' is changed master-database by this function called.
        $this->db = $this->load->database(DB_CONNECTION_WRITE, TRUE);

        return $this->db;
    }

    /**
     * sqlの戻り値が空の場合にnullを返す
     * (array()の場合)
     */
    public function return_null($array) {

        if (count($array) == 0) {

            return null;
        }

        return $array;
    }

    /**
     * idを指定してレコードを取得
     *
     * @param $ids     会員ID 配列
     * @param $columns カラム 配列
     * @param $del_flg 削除フラグ null の場合みない
     */
    public function get_many_ids($ids, $columns = null, $del_flg = 0) {

        $in = "";
        foreach ($ids as $value) {

            $in .= $in == "" ? "" : ",";
            $in .= $value;
        }

        $columns2 = "*";
        if ($columns != null) {

            $columns2 = "";
            foreach ($columns as $value) {

                $columns2 .= $columns2 == "" ? "" : ",";
                $columns2 .= $value;
            }
        }

        $table = strtolower($this->name);

        $del = " AND del_flg = 0";
        if ($del_flg === null) {

            $del = "";

        } else if ($del_flg === 1) {

            $del = " AND del_flg = 1";
        }

        $sql = "SELECT {$columns2} FROM {$table} WHERE id IN ({$in}) {$del}";

        $params = array();
        $res = $this->query($sql, $params);

        return $this->return_null($res);
    }

    public function get_date_to_master($sql, $args)
    {
        try{
            $data = $this->_get_master_db()->query($sql, $args);
            if(empty($data))
            {
                return;
            }
            return $data->result();
        }catch (Exception $e)
        {
            $trace = $e->getTrace();

            // kemi エラーログを吐く（traceが多すぎるので1階層目だけ）
            cake_log($trace[0]["args"], "error_query");

            throw $e;
        }
        //        return $this->db->query($sql, $args)->result();
    }

//    /**
//     * Get object by master-database.
//     *
//     * @return object	$this->db
//     * @author onishi
//     */
//    private function _get_master_db()
//    {
//
//        $this->load->database(DB_CONNECTION_WRITE);
//        $CI =& get_instance();
//
//        return $CI->db;
//    }

}