<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 用户数据模型
|--------------------------------------------------------------------------
|
*/
class Sys_log_model extends Common_model {
	
	const CURRENT_TABLE = 'sys_log';
	
	public function __construct()
	{
		$this->table_name = self::CURRENT_TABLE;
		parent::__construct();
	}
}