<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * |--------------------------------------------------------------------------
 * | 用户登录认证
 * |--------------------------------------------------------------------------
 */
class Auth
{

    public $user = array();

    public function __construct()
    {
    }

    /*
     * 输出日志
     */
    private function log_error($username = "", $password = "", $err = "")
    {
        H('my_array');
        $err_msg = "auth login:" . (string)get_ip() . ":{$username}:" . authcode($password, 'ENCODE') . ":" . var_export($err, true);
        log_message('error', $err_msg);
    }

    /**
     * 登录验证
     *
     * @param string $email 用户Email,不能和用户ID同时为空
     * @param string $password 未加密的密码,不能为空
     *
     * @return object
     */
    public function login($username, $password, $is_remember_me = false)
    {
//        // 判断用户IP是否被禁止
//        if ($this->check_ip_banned()) {
//            $this->log_error($username, $password, $this->retcode(107));
//            return $this->retcode(107);//你的IP已被禁用
//        }
        // 账号不能为空
        if (empty($username)) {
            return $this->retcode(101);//账号不能为空
        } else {
            //默认为小写
            $username = strtolower($username);
        }
        // 密码不能为空
        if (empty($password)) {
            return $this->retcode(103);//密码不能为空
        }

        // 判断用户是否存在
        $user = $this->get_user_model()->get_one(array('username' => $username));

        if (!isset($user['uid'])) {
            return $this->retcode(102);//账号不存在
        }

        //统计错误登录次数
        $redis_fail_login_times_key = C('redis_user_tmp_banned_pre') . $user['uid'];
        $fail_login_times = intval(get_redis()->get($redis_fail_login_times_key));
        // 判断错误尝试次数
        if ($fail_login_times >= 5) {
            if (!empty($user['mobile']) && $fail_login_times == 5) {
                //发送通知账号所有人,发送邮件
                //send_sms($user['mobile'], '您的后台账号' . $user['username'] . '错误次数登录过多，可能被入侵.');
                $this->log_error($username, $password, "send error sms/email");
            }
            $this->log_error($username, $password, $this->retcode(109));
            return $this->retcode(109);//登录错误次数过多，账号已被禁用.
        }

        // 判断密码是否正确
        $pass = password(substr($password, 0, 20), $user['encrypt']);

        if ($user['password'] != $pass) {
            //统计错误登录次数
            get_redis()->incr($redis_fail_login_times_key);
            //一分钟内的错误次数
            get_redis()->expire($redis_fail_login_times_key, 60);
            $this->log_error($username, $password, $this->retcode(104));
            return $this->retcode(104);//密码错误
        }
        //重置错误登录次数
        get_redis()->del($redis_fail_login_times_key);

        $uid = $user['uid'];

        //后台有设置ip白名单
//        if (isset($user['white_ip']) && !empty($user['white_ip'])) {
//            $white_ip = explode(',', $user['white_ip']);
//            $is_match = false;
//            foreach ($white_ip as $ip) {
//                if (get_ip() == $ip) {
//                    $is_match = true;
//                    break;
//                }
//            }
//            if (!$is_match) {
//                return $this->retcode(114);//登录错误次数过多，账号已被禁用.
//            }
//        }


        //禁用账号判断
        $status = $this->check_user_status($uid);
        switch ($status) {
            case 0:
                return $this->retcode(110);//账号未审核
                break;
            case 2:
                return $this->retcode(111);//账号不通过
                break;
            case 3:
                return $this->retcode(112);//账号已冻结
                break;
            case 4:
                return $this->retcode(113);//账号已停用
                break;
            case 1://账号正常状态
                //更新
                $update_date = array();
                $update_date['last_ip'] = get_ip();
                $update_date['last_login'] = time();
                $update_date['login_times'] = intval($user['login_times']) + 1;
                $this->get_user_model()->update(array('uid' => $uid), $update_date);

                // 注册passport的cookie
               $this->user = $user;
              $this->sign_user_info($this->user);
                return $this->retcode(0);
            default:
                return $this->retcode(201);
        }
    }

    /**
     * 登记用户的登录状态 (即: 注册cookie + 记录登录信息)
     *
     * @param array $user
     * @param boolean $is_remeber_me
     */
    public function sign_user_info($user = array())
    {
        del_sess(array('login_error_num', 'login_captcha'));
        $user['sing_time'] = time();
        $expire = get_sess('is_remember_me') ? (3600 * 24 * 365) : (3600 * 24 * 1);
        //获取通信的Code
        $code = json_encode($user);
        //加密code
        $code = authcode($code, 'ENCODE');
        //设置cookies
        set_cookie('u_i', $code, $expire, get_top_domain());
        //设置session
        set_sess($user);
        set_sess('u_i', json_encode($user));
        return $this->retcode(0);
    }

    /**
     * 通过加密code来登记用户的登录状态
     * @param string $code
     * @return object
     */
    public function sign_user_info_by_code($code = '')
    {
        $userinfo = $this->code_auth($code);
        if (is_array($userinfo)) {
            return $this->sign_user_info($userinfo);
        } else {
            return $this->retcode(1);
        }
    }

    /**
     * 获取用户登录信息
     * @return array
     */
    public function get_user_info()
    {
        $userinfo = get_cookie('u_i');

        if (!$userinfo) {
            $userinfo = get_sess('u_i');
            if ($userinfo) {
                //如果cookies为空，注册一下cookies
                $this->sign_user_info($userinfo);
                return $userinfo;
            } else
                return NULL;
        }

        $userinfo = $this->code_auth($userinfo);

        if (!empty($userinfo) && is_array($userinfo)) {
            return $userinfo;
        }
        return NULL;
    }

    /**
     * 验证code是否合法
     * @return array
     */
    public function code_auth($code)
    {
        if (empty($code)) {
            return NULL;
        }
        $result = json_decode(authcode($code, 'DECODE'), TRUE);

        if (is_array($result) && isset($result['uid']) && intval($result['uid']) > 0) {
            return $result;
        }

        return NULL;
    }

    /**
     * 清空用户登录状态
     *
     * @param array $user
     * @param boolean $is_remeber_me
     */
    public function clear_user_info()
    {
        delete_cookie('u_i', get_top_domain());
        //移除所有redis缓存
        del_user_all_session(get_sess('uid'));
        //清除所有session
        clean_sess();
    }

    /**
     * 验证用户IP是否被禁用
     *
     */
    public function check_ip_banned()
    {
        //禁用ip的缓存key
        $redis_cache_key = C('redis_user_banned_ips');
        $ip = get_ip();
        return get_redis()->sIsMember($redis_cache_key, $ip);
    }

    /**
     * 验证用户状态
     * 查看用户是否非正常
     */
    public function check_user_status($uid)
    {
        $user = $this->get_user_model()->get_one(array('uid' => $uid), 'status');
        return intval($user['status']);
    }

    /*
    |--------------------------------------------------------------------------
    | 增加新用户
    |--------------------------------------------------------------------------
    */

    // 增加用户
    // 会验证字段合法性
    // username, email, password
    public function add_user($user = array())
    {
        if (empty($user)) {
            return $this->retcode(201);
        }
        // 自动填充username email password
        if (isset($user['site']) && preg_match('/^((https|http)?:\/\/)/i', $user['site'])) {
            $url = parse_url($user['site']);
        }
        //用户名转换成小写
        $user['username'] = isset($user['username']) ? strtolower($user['username']) : '';
        $user['nickname'] = isset($user['nickname']) ? $user['nickname'] : '';
        $user['mobile'] = isset($user['mobile']) ? $user['mobile'] : '';
        $user['weixin'] = isset($user['weixin']) ? $user['weixin'] : '';
        $user['qq'] = isset($user['qq']) ? $user['qq'] : '';
        $user['email'] = isset($user['email']) ? $user['email'] : '';
        $user['password'] = isset($user['password']) ? $user['password'] : '';
        $user['password_admin'] = isset($user['password_admin']) ? $user['password_admin'] : '';
        $user['white_ip'] = isset($user['white_ip']) ? $user['white_ip'] : '';
        //默认通过审核
        $user['status'] = isset($user['status']) ? $user['status'] : 1;
        //默认未分组
        $user['gid'] = isset($user['gid']) ? intval($user['gid']) : 0;
        //默认无上级
        $user['p1'] = isset($user['p1']) ? intval($user['p1']) : 0;
        //默认无上上级
        $user['p2'] = isset($user['p2']) ? intval($user['p2']) : 0;
        //无设置注册站点情况下
        if (!isset($user['site']))
            $user['site'] = isset(get_user_refer()['site']) ? get_user_refer()['site'] : get_current_host();//默认获取当前域名

        unset($user['remember']);

        $validate = $this->add_validation($user);
        //检测
        if ($validate > 0) {
            return $this->retcode($validate);
        }

        // 填充默认数据
        $pass = password(substr($user['password'], 0, 20));
        $exend = array(
            'is_activated' => 0,//未激活
            'last_ip' => get_ip(),
            'login_ip' => '',
            'last_login' => 0,
            'create_at' => time(),
            'update_at' => time(),
            'login_times' => 0,
            'password' => $pass['password'],
            'encrypt' => $pass['encrypt'],
            'refer' => isset(get_user_refer()['refer']) ? get_user_refer()['refer'] : '',//注册来路
            'balance' => 0,
            'gift' => 0,
            'pincode' => '',
            'ads_domain' => '',
            'is_del' => 0

        );
        $user = array_merge($user, $exend);

        // 插入数据库
        $uid = $this->get_user_model()->insert($user, TRUE);

        //echo "uid: $uid";
        if ($uid) {
            //记录日志
            if ($uid <> intval(get_sess('uid')))
                user_action_log($uid, '账号被创建,操作员:' . get_sess('username') . '(' . get_sess('uid') . ')');

            //  user_detail表也需要添加一条记录
            $this->get_user_detail_model()->insert(array('uid' => $uid));

            //添加组成员
            if (isset($user['gid']) && intval($user['gid'] > 0)) {
                $g_data = array();
                $g_data['uid'] = $uid;
                $g_data['gid'] = $user['gid'];
                M("admin/sys_group_user_model")->insert($g_data);
            }

            //推荐人
            //是否直接设置推荐人
            $p1 = isset($user['p1']) ? intval($user['p1']) : 0;
            //更新等级
            $update_data = array();
            //无推荐人,自己为最顶级
            $update_data['rid'] = "|" . $uid . "|";
            //无直接设置推荐人，从域名来路获取
            if ($p1 == 0)
                $p1 = intval(get_user_refer()['p1']);
            if ($p1 > 0) {
                $one = M('admin/sys_user_model')->get_one(array('uid' => $p1), '', 'uid,rid,p1');
                if ($one) {
                    $update_data['p1'] = $p1;
                    $update_data['p2'] = $one['p1'];
                    $update_data['rid'] = $one['rid'] . "|" . $uid . "|";
                }
            }
            M('admin/sys_user_model')->update(array('uid' => $uid), $update_data);
            $user['rid'] = $update_data['rid'];
            $user['uid'] = $uid; //uid添加到$user数组
            //$this->sign_user_info($user);
            $this->user = $user;
            return $this->retcode(0);
        }
        return $this->retcode(4);
    }

    /**
     * 检测表单数据
     * @param $user_data array
     * @return mixed
     */
    protected function add_validation($user_data)
    {
        $code = 0;

        foreach ($user_data as $key => $val) {
            if ($key == 'username') {
                // 检测用户名是否合法
                if (!is_username($val)) {
                    $code = 203;
                    break;
                }
                // 检测用户名是否存在
                $user = $this->get_user_model()->get_user_by_field(array('username' => $val));
                if ($user) {
                    $code = 204;
                    break;
                }
            } elseif ($key == 'nickname') {
                if (!empty($val)) {
                    // 敏感词过滤，待加入
                    // 过滤敏感用户名
                    R('sensitive/sensitive');
                    if (CI()->sensitive->is_sensi_word($val)) {
                        $code = 202;
                        break;
                    }
                }
            } else if ($key == 'email') {
                if (!empty($val)) {
                    // 检测邮箱是否合法
                    if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $val)) {
                        $code = 205;
                        break;
                    }
                    // 检测邮箱是否存在
                    $user = $this->get_user_model()->get_user_by_field(array('email' => $val));
                    if ($user) {
                        $code = 206;
                        break;
                    }
                }
            } else if ($key == 'password') {
                if (!is_password($val)) {
                    $code = 207;
                    break;
                }
            } else if ($key == 'mobile') {
                if (!empty($val) && !is_mobile_number($val)) {
                    $code = 211;
                    break;
                }
            } else if ($key == 'weixin') {
                if (!empty($val) && !is_weixin($val)) {
                    $code = 212;
                    break;
                }
            } else if ($key == 'qq') {
                if (!empty($val) && !is_qq($val)) {
                    $code = 213;
                    break;
                }
            }
        }

        return $code;
    }

    // retcode
    private function retcode($code = 0, $msg = '')
    {
        if (empty($msg)) {
            return (object)array('ret' => $code, 'msg' => user_code_to_msg($code));
        } else {
            return (object)array('ret' => $code, 'msg' => $msg);
        }
    }

    /**
     * 用户模型
     * @return mixed
     */
    private function get_user_model()
    {
        return M('admin/sys_user_model');
    }

    /**
     * 用户详情表模型
     * @return mixed
     */
    private function get_user_detail_model()
    {
        return M('admin/sys_user_detail_model');
    }
}
