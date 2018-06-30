<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 用户数据模型
|--------------------------------------------------------------------------
|
*/
class Sys_config_model extends Common_model {
	
	const CURRENT_TABLE = 'sys_config';
	
	public function __construct()
	{
		$this->table_name = self::CURRENT_TABLE;
		parent::__construct();
	}
}