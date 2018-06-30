<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 后台用户基本管理
|--------------------------------------------------------------------------
|
*/

class User extends Backend_Controller
{
    public function __construct()
    {
        parent::__construct();

        //模型名称
        $this->model_name = "admin/sys_user_model";
    }

    public function index()
    {
        $this->show('user_view');
    }

    /**
     * 新增/修改接口
     */
    public function save()
    {
        if (empty($this->model_name)) {
            $this->render_ajax(1, '模型Model未定义');
            exit;
        }

        //主键名称
        $key = M($this->model_name)->get_pk();
        $key_value = $this->post($key);
        //过滤数据，去除非数据库字段数据
        $pdata = $this->prep_data(M($this->model_name)->table_name, FALSE);

        //自定义数据处理
        //如果需要处理密码
        $password = (string)$this->post('password2');
        if (!empty($password)) {
            if ($key_value == 1 && $this->G['uid'] != 1) {
                $this->render_ajax(1, '不允许修改超级管理员密码');
            }
        }
        $pdata['password'] = $password;

        //存在主键为更新，否则新增
        if (is_post() && intval($this->post($key)) > 0) {
            //自定义附加条件

            //手机号码格式
            if (!empty($pdata['mobile']) && !is_mobile_number($pdata['mobile'])) {
                $this->render_ajax(1, '数据保存失败,手机格式错误.');
            }

            //微信号码格式
            if (!empty($pdata['weixin']) && !is_weixin($pdata['weixin'])) {
                $this->render_ajax(1, '数据保存失败,手机格式错误.');
            }

            //qq号码格式
            if (!empty($pdata['qq']) && !is_qq($pdata['qq'])) {
                $this->render_ajax(1, '数据保存失败,手机格式错误.');
            }

            //邮箱必须唯一
            if (!empty($pdata['email'])) {
                if (!is_email($pdata['email'])) {
                    $this->render_ajax(1, '数据保存失败,邮箱格式错误.');
                }
                $this->db->select('uid')->from(M($this->model_name)->table_name)->limit(1, 0);
                $this->db->where('email', $pdata['email']);
                $this->db->where_not_in($key, $key_value);
                if ($this->db->count_all_results() > 0) {
                    $this->render_ajax(1, '数据保存失败,邮箱:' . $pdata['email'] . '已经存在.');
                }
            }
            //不可修改分组、上级、余额等
            unset($pdata['gid']);//分组
            unset($pdata['rid']);//关联关系
            unset($pdata['p1']);//上级
            unset($pdata['p2']);//上上级
            unset($pdata['balance']);//余额
            unset($pdata['gift']);//礼金
            unset($pdata['pincode']);//PIN密码
            unset($pdata['is_del']);//逻辑删除状态

            //密码加密
            $pass = password(substr($password, 0, 20));
            $pdata['password'] = $pass['password'];
            $pdata['encrypt'] = $pass['encrypt'];

            if (!empty($pdata['password'])) {
                //密码复杂性判断
                $pass = password(substr($password, 0, 20));
                $pdata['password'] = $pass['password'];
                $pdata['encrypt'] = $pass['encrypt'];
            }

            //不允许修改用户名
            unset($pdata['username']);
            $pdata['update_at'] = time();//更新时间

            //执行数据库操作
            M($this->model_name)->update(array($key => $key_value), $pdata);
            //日志调试输出
            //log_message('debug', "\npdata:\n" . var_export($pdata, true));
            //log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));

            $this->render_ajax(0, '更新数据成功');

        } else if (is_post() && empty($key_value)) {
            //新增用户
            $pdata['site'] = '后台添加';
            //不可修改分组、上级、余额等
            unset($pdata['gid']);//分组
            unset($pdata['rid']);//关联关系
            unset($pdata['p1']);//上级
            unset($pdata['p2']);//上上级
            unset($pdata['balance']);//余额
            unset($pdata['gift']);//礼金
            unset($pdata['pincode']);//PIN密码

            $result = (array)$this->auth->add_user($pdata);
            if ($result['ret'] == 0) {
                $this->render_ajax(0, '添加用户成功');
            } else {
                $this->render_ajax($result['ret'], '添加用户失败.' . $result['msg']);
            }
            //日志调试输出
            log_message('debug', "\npdata:\n" . var_export($pdata, true));
            log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));

        } else {
            $this->render_ajax(1, '数据保存失败');
        }
    }

    /**
     * 删除接口
     */
    public function del()
    {
        //默认提交的字段名称是id
        $ids = (array)$this->post("id");

        //自定义附加条件
        foreach ($ids as $id) {
            if ($id == 1) {
                $this->render_ajax(1, '不允许删除超级管理员');
            } elseif ($id < 10) {
                $this->render_ajax(1, '不允许删除系统默认账号');
            }
        }
        //逻辑删除
        $pdata['is_del'] = 1;
        //
        $pdata['status'] = 4;
        foreach ($ids as $id) {
            //逻辑删除
            if (M($this->model_name)->update(array('uid' => $id), $pdata))
                user_action_log($id, '账号被删除,操作员:' . get_sess('username') . '(' . get_sess('uid') . ')');
        }
        $this->render_ajax(0, '删除成功');
    }

    /**
     * 判断会员是否在线
     */
    public function check_online($uid = 0)
    {
        //从redis取出最后活动时间，并判断session_id是否存在于redis中（被踢）
        if (check_user_session_status(intval($uid))) {
            $this->render_ajax(0, '会员在线.');
        } else {
            $this->render_ajax(1, '会员离线.');
        }
    }

    /**
     * 下线会员进程
     */
    public function offline_by_sesseion_id()
    {

        //默认提交的字段名称是id
        $id = $this->post('id');
        $uid = intval($this->post('uid'));
        if (!empty($id)) {

            foreach ($id as $session_id) {
                //下线用户
                user_offline_by_session_id($session_id);
            }

            user_action_log($uid, '被踢下线,操作员:' . get_sess('username') . '(' . get_sess('uid') . ')');
            $this->render_ajax(0, '会员下线成功.');
        }
    }

    //返回在线总数
    public function public_online_num()
    {
        $this->render_ajax(0, get_user_online_num());
    }

    /*
     * 显示用户日志
     */
    public function log()
    {
        $uid = intval($this->post('uid'));
        //管理权限控制

        $where['uid'] = $uid;
        $data = parent::search_grid($where, 'admin/sys_user_log_model', '*');
        echo json2code($data);
    }

    /*
     * 输出用户的session信息
     */
    public function session()
    {
        $uid = intval($this->post('uid'));
        //管理权限控制
        $data = get_user_session_list($uid);
        echo json_encode(array('total' => is_array($data) ? count($data) : 0, 'rows' => $data));
    }

}
