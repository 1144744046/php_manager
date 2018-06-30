<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 基础公共数据模型  
|--------------------------------------------------------------------------
*/

class Common_model extends CI_Model
{

    // 数据表名
    public $table_name = '';
    public $primary_key = '';

    public function __construct($group_name="")
    {
        parent::__construct();
        // 数据库连接
        $this->load->database($group_name);
    }

    /**
     * 执行sql查询
     *
     * @param $where         查询条件[例`name`='$name'] 或者 array('name'=>'$name');
     * @param $data          需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $limit         返回结果范围[例：10或10,10 默认为空]
     * @param $order         排序方式    [默认按数据库默认方式排序
     *
     * @return array        查询结果集数组
     */
    public function get_all($where = array(), $limit = '', $order_by = '', $select = '*')
    {
        $this->db->select($select);

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($key == 'like') {
                    /*
                        like查询支持 value传二维数组
                        $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                        WHERE title LIKE '%match%' AND page1 LIKE '%match%' AND page2 LIKE '%match%'
                     */
                    $this->db->like($value);
                    continue;
                } /*
                            in查询支持 value传二维数组
                            $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                            WHERE title in 'match' AND page1 in 'match' AND in 'match'
                     */
                elseif ($key == 'in' && is_array($value) && count($value) == 2) {
                    $this->db->where_in($value[0], $value[1]);
                } elseif ($key == 'not_in' && is_array($value) && count($value) == 2) {
                    $this->db->where_not_in($value[0], $value[1]);
                } elseif (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }
        if (!empty($limit)) {
            $this->db->limit($limit[0], $limit[1]);
        }
        $result = $this->db->get($this->table_name)->result_array();
        return $result;

    }

    /**
     * 获取单条记录查询
     * @param $where         查询条件
     * @param $data          需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $order         排序方式    [默认按数据库默认方式排序]
     *
     * @return array/null    数据查询结果集,如果不存在，则返回空
     */
    public function get_one($where = array(), $order = '', $select = '*')
    {
        $this->db->select($select)->from($this->table_name)->limit(1, 0);
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($key == 'like') {
                    /*
                        like查询支持 value传二维数组
                        $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                        WHERE title LIKE '%match%' AND page1 LIKE '%match%' AND page2 LIKE '%match%'
                     */
                    $this->db->like($value);
                    continue;
                } /*
                        in查询支持 value传二维数组
                        $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                        WHERE title in 'match' AND page1 in 'match' AND in 'match'
                 */
                elseif ($key == 'in' && is_array($value) && count($value) == 2) {
                    $this->db->where_in($value[0], $value[1]);
                } elseif ($key == 'not_in' && is_array($value) && count($value) == 2) {
                    $this->db->where_not_in($value[0], $value[1]);
                } elseif (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }

        }
        if (!empty($order)) {
            $this->db->order_by($order);
        }
        $query = $this->db->get()->result_array();
        return isset($query[0]) ? $query[0] : false;
    }

    /**
     * 执行添加记录操作
     * @param $data             要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param $return_insert_id 是否返回新建ID号
     * @param $replace          是否采用 replace into的方式添加数据
     *
     * @return boolean
     */
    public function insert($data, $return_id = FALSE)
    {
        if (empty($data)) {
            return FALSE;
        }
        if ($this->db->insert($this->table_name, $data)) {
            if ($return_id) {
                return $this->insert_id();
            }
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 执行批量添加记录操作
     * @param $data 要增加的数据，参数为多维数组array(array1, array2)。
     *
     * @return boolean
     */
    public function insert_batch($data, $table = NULL)
    {
        if (empty($data)) {
            return FALSE;
        }

        $table = !$table ? $this->table_name : $table;
        if ($this->db->insert_batch($table, $data)) {
            return TRUE;
        };

        return FALSE;
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    public function insert_id()
    {
        return $this->db->insert_id();
    }

    /**
     * @param $data 主键id不存在就插入，存在就替换
     * @return bool
     */
    public function replace($data)
    {
        if (empty($data)) {
            return FALSE;
        }
        if ($this->db->replace($this->table_name, $data)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 执行更新记录操作
     * @param $where         更新数据条件,不充许为空。
     * $data = array(field=>val,field2=>val2)
     * @return boolean
     */
    public function update($where = array(), $data)
    {
        if (empty($where)) {
            return false;
        }

        if (method_exists($this, "_before_update")) {
            $this->_before_update($data);
        }

        if ($this->db->where($where)->update($this->table_name, $data)) {
            return true;
        }

        return false;
    }

    //批量更新
    public function update_batch($data, $field = 'id')
    {
        if (empty($data)) {
            return false;
        }
        if ($this->db->update_batch($this->table_name, $data, $field) !== FALSE) {
            return true;
        }
        return false;
    }

    /**
     * @param $where array('id'=>array(id1,id2,id3))
     * $data 更新数据 array('check'=>1);
     * @return bool
     */
    public function update_in($where = array(), $data = array())
    {
        if (empty($where)) {
            return false;
        }
        $field = array_keys($where);
        $values = array_values($where);
        $this->db->where_in($field[0], $values[0]);
        if ($this->db->update($this->table_name, $data)) {
            return true;
        }
        return false;
    }

    /**
     * 更新统计字段, 比如val = val+1
     * @param $where array
     * @param $field string  table field
     * @param $operator string  +number/-number
     * @return bool
     */
    public function update_count_field($where, $field, $operator = '+1')
    {
        $this->db->set($field, $field . $operator, false)
            ->where($where)
            ->update($this->table_name);
        if ($this->db->affected_rows() <= 0) {
            return false;
        }

        return true;
    }


    /**
     * [批量更新统计字段]
     * @param  [type] $where    [array(field=>array(id1,id2,id3))]
     * @param  [string] $field    [需要更新字段]
     * @param  string $operator [description]
     * @return [type]           [+X/-X]
     */
    public function update_count_field_batch($where, $field, $operator = '+1')
    {
        if (empty($where)) {
            return FALSE;
        }
        $where_field = current(array_keys($where));
        $where_arr = current(array_values($where));
        $this->db->set($field, $field . $operator, false)
            ->where_in($where_field, $where_arr)
            ->update($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 执行删除记录操作
     * @param $where         删除数据条件,不充许为空。
     *
     * @return boolean
     */
    public function delete($where = array(), $tables = '')
    {
        if (empty($where)) {
            return FALSE;
        }
        if (empty($tables)) {
            $tables = $this->table_name;
        }
        if ($this->db->where($where)->delete($tables)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 删除多条记录记录操作
     * @param $fields = array(field => array(id1, id2, id3))         删除数据条件,不充许为空。
     *
     * @return boolean
     */
    public function delete_batch($fields = array(), $tables = '')
    {
        if (empty($fields)) {
            return FALSE;
        }
        if (empty($tables)) {
            $tables = $this->table_name;
        }
        $field = array_keys($fields);
        $values = array_values($fields);
        $this->db->where_in($field[0], $values[0]);
        if ($this->db->delete($tables)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 计算记录数
     * @param string /array $where 查询条件
     */
    public function get_count($where = '')
    {
        if (!empty($where)) {
            //旧的不支持in条件
            //$this->db->where($where);
            foreach ($where as $key => $value) {
                if ($key == 'like') {
                    /*
                        like查询支持 value传二维数组
                        $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                        WHERE title LIKE '%match%' AND page1 LIKE '%match%' AND page2 LIKE '%match%'
                     */
                    $this->db->like($value);
                    continue;
                } /*
                            in查询支持 value传二维数组
                            $array = array('title' => $match, 'page1' => $match, 'page2' => $match);
                            WHERE title in 'match' AND page1 in 'match' AND in 'match'
                     */
                elseif ($key == 'in' && is_array($value) && count($value) == 2) {
                    $this->db->where_in($value[0], $value[1]);
                } elseif ($key == 'not_in' && is_array($value) && count($value) == 2) {
                    $this->db->where_not_in($value[0], $value[1]);
                } elseif (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        return $this->db->count_all_results($this->table_name);
    }

    /**
     * 获取最后数据库操作影响到的条数
     * @return int
     */
    public function affected_rows()
    {
        return $this->db->affected_rows();
    }

    /**
     * 获取表字段
     * @param string $table_name 表名
     *
     * @return array
     */
    public function get_fields($table_name = '')
    {
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        return $this->db->list_fields($table_name);
    }

    /**
     * 检查表是否存在
     * @param $table 表名
     *
     * @return boolean
     */
    public function table_exists($table = '')
    {
        if (empty($table)) {
            return FALSE;
        }
        return $this->db->table_exists($table);
    }

    /**
     * 检查字段是否存在
     * @param $field 字段名
     *
     * @return boolean
     */
    public function field_exists($field = '')
    {
        $fields = $this->get_fields($this->table_name);
        return in_array($field, $fields);
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param $value 数组值
     */
    public function add_special_char(&$value)
    {
        if ('*' == $value || FALSE !== strpos($value, '(') || FALSE !== strpos($value, '.') || FALSE !== strpos($value, '`')) {
            //不处理包含* 或者 使用了sql方法。
        } else {
            $value = '`' . trim($value) . '`';
        }
        return $value;
    }

    /**
     * 对字段值两边加引号，以保证数据库安全
     * @param $value 数组值
     * @param $key   数组key
     * @param $quotation
     */
    public function escape_string(&$value, $key = '', $quotation = 1)
    {
        if ($quotation) {
            $q = '\'';
        } else {
            $q = '';
        }
        $value = $q . $value . $q;
        return $value;
    }

    /**
     * 返回数据库版本号
     */
    public function version()
    {
        return $this->db->version();
    }

    /**
     * 返回指定表中字段信息
     */
    public function get_field_val($table_name = '', $map = array(), $show_val)
    {
        $row_data = $list = $this->db->get_where($table_name, $map)->result_array();
        return !empty($row_data) ? $row_data[0][$show_val] : '';
    }

    /**
     * 获取指定表名的主键
     * @param $table_name
     */
    public function get_pk($table_name = '')
    {
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        $list_fields = $this->db->field_data($table_name);
        foreach ($list_fields as $fields) {
            if ($fields->primary_key == 1) {
                $pkey = $fields->name;
                break;
            }
        }
        $pkey = !empty($pkey) ? $pkey : NULL;
        return $pkey;
    }

    /**
     * 列出此表的所有字段
     */
    public function list_fields($table_name = '')
    {
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        $fields = $this->db->list_fields($table_name);
        return $fields;
    }

    /**
     * 清空表
     * @return array
     */
    public function truncate_table($table_name = '')
    {
        if (empty($table_name)) {
            $table_name = $this->table_name;
        }
        return $this->db->truncate($table_name);
    }
}