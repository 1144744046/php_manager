<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 用户数据模型
|--------------------------------------------------------------------------
|
*/
class Sys_user_model extends Common_model {
	
	const CURRENT_TABLE = 'sys_user';
	
	public function __construct()
	{
		$this->table_name = self::CURRENT_TABLE;
		parent::__construct();
	}
    /**
     * 根据用户字段获取用户
     * 用于检测用户名和邮箱是否存在
     * @param $uid number
     * @return mixed
     * author llq
     */
    public function get_user_by_field($arr)
    {
        $field = current(array_keys($arr));
        $query = $this->db->select('uid,email,username')
            ->where(array($field => $arr[$field]))
            ->limit(1)
            ->get(self::CURRENT_TABLE);
        $result = $query->result_array();

        if(!empty($result)) {
            $result = current($result);
            return $result;
        }

        return false;
    }
}