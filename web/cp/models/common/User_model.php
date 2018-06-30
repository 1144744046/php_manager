<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/2/3
 * Time: 18:04
 */

class User_model extends Common_model
{
    const CURRENT_TABLE = 'sys_user';

    public function __construct()
    {
        $this->table_name = self::CURRENT_TABLE;
        parent::__construct();
    }
}