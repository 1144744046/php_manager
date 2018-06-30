<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12-9-28
 * Time: 下午12:56
 * To change this template use File | Settings | File Templates.
 */
class Sys_group_user_model extends Common_model
{

    const TBNAME = 'sys_group_user';

    function __construct()
    {
        $this->table_name = self::TBNAME;
        parent::__construct();
    }

    public function get_member_by_gid($where= array(), $limit = '', $order_by = '')
    {
        if (isset($where['username']) && !empty($where['username'])) {
            $this->db->like('sys_user.username', $where['username']);
        }
        if (empty($where['gid'])) {
            $this->db->where('sys_group_user.gid', '');
        } elseif (is_array($where['gid'])) {
            $this->db->where_in('sys_group_user.gid', $where['gid']);
        } else {
            $this->db->where('sys_group_user.gid', $where['gid']);
        }
        $this->db->select('sys_group_user.gid,sys_group_user.customperm,sys_user.*,sys_group.gname');
        $this->db->from('sys_group_user');
        $this->db->join('sys_user', 'sys_user.uid = sys_group_user.uid');
        $this->db->join('sys_group', 'sys_group.gid = sys_group_user.gid');

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }

        if (!empty($limit)) {
            $this->db->limit($limit[0], $limit[1]);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete_member($post)
    {
        if (count($post['cpuid']) > 0) {
            $this->db->where_in('uid', $post['cpuid']);
            $this->db->delete($this->table_name);
        }
    }

    public function add_member($post)
    {
        if (count($post['cpuid']) > 0) {
            $this->db->where_in('uid', $post['cpuid']);
            $this->db->delete($this->table_name);
        }
    }

    public function get_user_admincp($uid)
    {
        $this->db->select('sys_group.*');
        $this->db->from('sys_group_user');
        $this->db->join('sys_group', 'sys_group_user.gid = sys_group.gid');
        $this->db->where('sys_group_user.uid', $uid);
        $query = $this->db->get();
        $result = $query->result();
        return isset($result[0]) ? $result[0] : '';
    }

    public function update_insert($data)
    {
        $this->db->set($data);
        try {
            $this->db->replace($this->table_name);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}