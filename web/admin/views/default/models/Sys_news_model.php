<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 站内消息模型
|--------------------------------------------------------------------------
|
*/

class Sys_news_model extends Common_model
{

    const CURRENT_TABLE = 'sys_news';

    public function __construct()
    {
        $this->table_name = self::CURRENT_TABLE;
        parent::__construct();
    }
}