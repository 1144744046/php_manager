<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台管理员的添加
 * 后台管理员的权限的分配
 *
 *
 *
 */
class Role extends Backend_Controller
{
    public function __construct()
    {
        parent::__construct();

        //模型名称
        $this->model_name = "admin/sys_group_model";
    }

    public function search()
    {
        $result = parent::search_by_pdata();
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            //0为正确信息
            if (intval($result[0]) == 0) {
                $tree_data = array();
                foreach ($result[1]['rows'] as $row) {
                    if (intval($row['parentid']) > 0)
                        $row['_parentId'] = $row['parentid'];
                    $tree_data[] = $row;
                }
                $result[1]['rows'] = $tree_data;
                echo json_encode($result[1]);
            } else {
                $this->render_ajax($result[0], $result[1]);
            }
        }
        exit;
    }

    public function get_all_role()
    {
        $result = M($this->model_name)->get_role(0, true);
        echo json_encode($result);
    }

    public function save()
    {
        $pdata = $this->post();
        if (is_post() && intval($this->post('gid') > 0)) {
            M($this->model_name)->update(array('gid' => $pdata['parentid']), array('islast' => 0));
            M($this->model_name)->update(array('gid' => $pdata['gid']), $pdata);
            $this->render_ajax(0, '修改成功', array('gid' => $this->post('gid')));
        } else if (is_post() && !intval($this->post('gid'))) {
            $pdata['islast'] = 1;
            unset($pdata['gid']);
            M($this->model_name)->update(array('gid' => $pdata['parentid']), array('islast' => 0));
            $gid = M($this->model_name)->insert($pdata, TRUE);
            $this->render_ajax(0, '新增成功', array('gid' => $gid));
        } else {
            $this->render_ajax(1, '保存失败');
        }
    }

    /**
     * 删除角色
     */
    public function del()
    {
        if (empty($mname)) {
            $mname = $this->model_name;
        }

        if (empty($mname)) {
            $this->render_ajax(1, '模型Model未定义');
            exit;
        }

        //主键名称
        $key = M($mname)->get_pk();

        //默认前台提交的字段名称是id
        $ids = (array)$this->post("id");

        //自定义附加条件
        foreach ($ids as $id) {
            if ($id == C('super_admin_gid')) {
                $this->render_ajax(1, '超级管理员角色不允许删除.');
            } elseif ($id < 46) {
                $this->render_ajax(1, '系统默认账号角色不允许删除 .');
            }
            $where['gid'] = $id;

            //判断角色是否存在
            $role = M($this->model_name)->get_one($where);
            if (!$role) {
                $this->render_ajax(1, '角色不存在');
            }

            //判断是否还有成员
            $user = M('admin/sys_group_user_model')->get_member_by_gid($where);
            if ($user) {
                $this->render_ajax(1, '角色ID:' . $id . '(' . $role['gname'] . ') 还有用户成员，请先删除');
            }
        }

        //开始删除
        if (is_post() && is_array($ids) && M($mname)->delete_batch((array($key => $ids)))) {
            $this->render_ajax(0, '删除成功');
        } else {
            $this->render_ajax(1, '删除失败');
        }
    }

    /**
     *角色列表
     *
     */
    public function index()
    {
        $this->show('role_view');
    }

    /**
     *角色成员
     *
     */
    public function list_user()
    {
        $gid = intval($this->post('gid'));
        $where['gid'] = $gid;
        $user = M('admin/sys_group_user_model')->get_member_by_gid($where);
        $data['total'] = count($user);
        $data['rows'] = $user;

        echo json_encode($data);
    }

    /**
     * 添加角色成员
     */
    public function save_user()
    {
        $gid = intval($this->post('gid'));
        if ($gid < 1) {
            $this->render_ajax(1, '未选择角色，更新数据失败');
        }
        if (is_post()) {
            $user = M('admin/sys_user_model')->get_one(
                array(
                    'username' => $this->post('username'),
                    //'email' => $this->post('email')
                ));
            if (isset($user['uid'])) {
                $admin_member = M('admin/sys_group_user_model')->get_one(array('uid' => $user['uid']));
                if ($admin_member) {
                    $this->render_ajax(1, '用户已存在于角色编号：' . $admin_member['gid']);
                } else {
                    if (M('admin/sys_group_user_model')->insert(array('uid' => $user['uid'], 'gid' => $gid, 'customperm' => 0))) {
                        //更新用户表gid字段
                        M('admin/sys_user_model')->update(array('uid' => $user['uid']), array('gid' => $gid));
                        $this->render_ajax(0, '更新数据成功');
                    } else {
                        $this->render_ajax(1, '更新数据失败');
                    }
                }
            } else {
                $this->render_ajax(1, '用户名不存在.');
            }
        }
        $this->render_ajax(1, '更新数据失败');

    }

    /**
     *删除角色成员
     *
     */
    public function del_user()
    {
        //默认提交的字段名称是id
        $ids = (array)$this->post("id");

        //自定义附加条件
        foreach ($ids as $id) {
            if ($id == 1) {
                $this->render_ajax(1, '不允许删除超级管理员');
            }
        }

        //开始删除
        if (is_post() && is_array($ids) && M('admin/sys_group_user_model')->delete_batch((array('uid' => $ids)))) {
            foreach ($ids as $id) {
                if (M('admin/sys_group_user_model')->delete((array('uid' => $id))))
                    M('admin/sys_user_model')->update(array('uid' => $id), array('gid' => 0));
            }
            $this->render_ajax(0, '删除成功');
        } else {
            $this->render_ajax(1, '删除失败');
        }
    }

    public function get_priv_menu()
    {
        $gid = intval($this->post("gid"));
        if ($gid > 0) {
            //gid

            if ($gid == 1) {
                $result = M('admin/sys_menu_model')->get_all('', '', '', 'id');

            } else {
                $result = M('admin/sys_group_priv_model')->get_all(array('gid' => $gid), '', '', 'menuid as id');
            }
            if ($result) {
                $ids = array();
                foreach ($result as $key => $value) {
                    $ids[] = $result[$key]['id'];
                }
                $this->render_ajax(0, json_encode($ids));
            } else {
                $this->render_ajax(0, '权限菜单未初始化');
            }
        }
        $this->render_ajax(1, '获取权限菜单失败');
    }

    /**
     *角色权限
     */
    public function save_priv()
    {
        //默认前台提交的字段名称是id
        $ids = (array)$this->post("id");

        //自定义附加条件
        $gid = intval($this->post('gid'));
        if ($gid < 1) {
            $this->render_ajax(1, '未选择角色');
        }
        if ($gid == 1) {
            $this->render_ajax(1, '不允许操作超级管理员权限');
        }

        $where['id'] = $ids;

        $menu = M('admin/sys_menu_model')->get_all($where);
        $data = array();
        if ($menu) {
            foreach ($menu as $m) {
                $data[] = array(
                    'gid' => $gid,
                    'menuid' => $m['id']
                );
            }

            //先删除所有权限
            if (M('admin/sys_group_priv_model')->delete(array('gid' => $gid))) {
                //重新添加所有权限
                if (M('admin/sys_group_priv_model')->insert_batch($data)) {
                    $this->render_ajax(0, '更新权限成功');
                } else {
                    $this->render_ajax(1, '更新权限失败');
                }
            } else {
                $this->render_ajax(1, '更新权限失败');
            }
        }

    }

}
