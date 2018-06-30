<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-9-28
 * Time: 下午12:55
 * To change this template use File | Settings | File Templates.
 */
class Sys_group_priv_model extends Common_model
{
    const TBNAME = 'sys_group_priv';

    function __construct()
    {
        $this->table_name = self::TBNAME;
        parent::__construct();
    }
}