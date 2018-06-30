<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/24
 * Time: 22:32
 */

class Login extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
   public function index(){

       // 获取表单数据
       $post = $this->input->post(NULL, true);
       //var_dump($post);
       // 设置返回页面
       $redirect = $this->input->get('referer') ? urldecode($this->input->get('referer')) : "/admin/main/public_index";
       // 只接受ajax登录
       if ($this->input->is_ajax_request()) {

           //判断用户名/密码
           $ret = $this->auth->login($post['username'], $post['password'], $post['remember']);

           //成功登录
          if ($ret->ret === 0) {
               $user = $this->auth->user;

               //判断是否是管理组
               if ($this->check_admin($user)) {
                   //记录日志
                   user_action_log(intval($user['uid']), user_code_to_msg(0), 1);

                   /*
                   //同时一个用户只允许一个ip登录
                   $login_limit = intval(sys_config_get('SYS_USER_IP_SINGLE'));
                   if ($login_limit == -1 || ($login_limit == 1 && get_ip() <> $user['last_ip'])) {
                       sys_user_offline_by_session_id($user['session_id']);
                   }
                   */
                   //注册cookies
                   $this->auth->sign_user_info($user);
                   $this->render_ajax(0, '登录成功.', array('url' => $redirect));
               } else {
                   $this->render_ajax(1, '登录错误.您没有管理权限.');
               }

           } else {
               //记录密码错误行为
               if ($ret->ret === 104) {
                   $error_msg = "密码错误:" . substr($post['password'], 0, -3) . "*** [" . authcode($post['password'], "ENCODE") . "]";
                   $one = M('admin/sys_user_model')->get_one(array('username' => $post['username']));
                   if ($one) {
                       //记录日志
                       user_action_log(intval($one['uid']), $error_msg, 1);
                   }
               } else {
                   //记录日志
                   $one = M('admin/sys_user_model')->get_one(array('username' => $post['username']));
                   if ($one) {
                       //记录日志
                       user_action_log(intval($one['uid']), $ret->msg, 1);
                   }
               }

               $this->render_ajax(1, $ret->msg);
           }

       } elseif (get_sess('is_admin') && get_sess('user_info')) {
           header("Pragma: no-cache");
           redirect('/admin/main/public_index', 'Location', 302);
      } else {
           $data = array('redirect' => $redirect);
           $this->show("login_view", $data);
       }


   }

    /**
     * 判断是否有管理权限
     * @param $user
     * @return bool
     */
    protected function check_admin($user)
    {
        // 判断是否是后台管理用户
        $admin_users = M('admin/sys_user_model')->get_one(array('uid' => $user['uid']));

        // 记录登录状态 // 判断是否后台管理组 或 子管理员组 或 创始人
        if ((!empty($admin_users) && (check_is_backend_admin($admin_users))) || checkfounder($user)) {
            $sessions = array(
                'is_admin' => 1
            );
//            //设置 session
           set_sess($sessions);
            return true;
        } else {
            return false;
        }
    }
    // 登出后台
    public function logout()
    {
        header("Pragma: no-cache");
        $this->auth->clear_user_info();
        $referer = $this->request('referer');
        redirect('/admin/login?referer=' . $referer, '', 302);
    }
}
