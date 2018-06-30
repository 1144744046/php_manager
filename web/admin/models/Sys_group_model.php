<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-9-28
 * Time: 下午12:55
 * To change this template use File | Settings | File Templates.
 */
class Sys_group_model extends Common_model
{

    const TBNAME = 'sys_group';

    function __construct()
    {
        $this->table_name = self::TBNAME;
        parent::__construct();
    }

    public function get_role($id = 0, $display_all = FALSE, $display_nodes_id = array())
    {
        $all_role = $this->get_all('', '', 'gid asc', '*,gid as id,gname as text');
        R('EasyuiTree');
        $this->easyuitree->set_nodes($all_role);
        $nodes = $this->easyuitree->get_tree_nodes($id, $display_all, $display_nodes_id);
        return $nodes;
    }

    public function get_children($uid = 0, $nickname = '')
    {
        if ($uid == 0) {
            return array();
        }
        $role = M('admin/sys_group_model')->get_user_admincp($uid);
        if (!$role) {
            return array();
        }
        $group_id = $role->gid;
        $data = $this->get_all(array('parentid' => $group_id), '', 'gid asc', 'gid');
        $children = array();
        foreach ($data as $child) {
            array_push($children, intval($child['gid']));
        }
        $members = M('admin/sys_group_model')->get_member_by_gid(array('gid' => $children));
        foreach ($members as $k => $m) {
            $members[$k + 1] = array(
                'uid' => $m['uid'],
                'nickname' => $m['nickname'],
            );
        }
        $members[0] = array(
            'uid' => $uid,
            'nickname' => $nickname,
        );
        return $members;
    }
}