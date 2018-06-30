<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 用户数据模型
|--------------------------------------------------------------------------
|
*/

class Sys_config_menu_model extends Common_model
{

    const CURRENT_TABLE = 'sys_config_menu';

    public function __construct()
    {
        $this->table_name = self::CURRENT_TABLE;
        parent::__construct();
    }

    /*
     * 获取所有菜单
     */
    public function get_menus($id = 0, $display_all = FALSE, $display_nodes_id = array())
    {
        $all_menu = $this->get_all('', '', 'listorder desc', '*,name as text,icon as iconCls');
        R('EasyuiTree');
        $this->easyuitree->set_nodes($all_menu);
        $nodes = $this->easyuitree->get_tree_nodes($id, $display_all, $display_nodes_id);
        return $nodes;
    }
}