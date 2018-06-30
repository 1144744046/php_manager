<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| 用户操作函数
| -------------------------------------------------------------------
*/

/*
 * 根据uid获取username
 */
if (!function_exists('get_username_by_uid')) {
    function get_username_by_uid($uid = 0)
    {
        $uid = intval($uid);
        $redis_key = C('redis_user_uid_username_pre') . $uid;
        $username = get_cache($redis_key);
        if (empty($username)) {
            $one = M('admin/sys_user_model')->get_one(array('uid' => $uid), '', 'username');
            if ($one) {
                $username = $one['username'];
                set_cache($redis_key, $username, 365 * 24 * 3600);
            }
        }
        return $username;
    }
}

/*
 * 根据uid获取所有个人信息
 */
if (!function_exists('get_userinfo_by_uid')) {
    function get_userinfo_by_uid($uid = 0)
    {
        $uid = intval($uid);
        $one = M('admin/sys_user_model')->get_one(array('uid' => $uid));
        if ($one) {
            return $one;
        }
        return false;
    }
}


/*
 * 将多条包含uid的数组添加username
 */
if (!function_exists('add_username_to_user_list')) {
    function add_username_to_user_list($user_list = array())
    {
        $result = array();
        if (!empty($user_list)) {
            foreach ($user_list as $user) {
                if (isset($user['uid']))
                    $user['username'] = get_username_by_uid($user['uid']);
                $result[] = $user;
            }
        }
        return $result;
    }
}


/*
 * 根据gid获取gname
 */
if (!function_exists('get_gname_by_gid')) {
    function get_gname_by_gid($gid = 0)
    {
        $gid = intval($gid);
        $redis_key = C('redis_user_gid_gname_pre') . $gid;
        $gname = get_cache($redis_key);
        if (empty($gname)) {
            $one = M('admin/sys_group_model')->get_one(array('gid' => $gid), '', 'gname');
            if ($one) {
                $gname = $one['gname'];
                set_cache($redis_key, $gname, 365 * 24 * 3600);
            }
        }
        return $gname;
    }
}

/*
 * 获取用户id
 */
if (!function_exists('get_uid')) {
    function get_uid()
    {
        if ($user = get_userinfo()) {
            if (isset($user['uid']))
                return intval($user['uid']);
        }
        return 0;
    }
}


/*
 * 获取用户名
 */
if (!function_exists('get_username')) {
    function get_username()
    {
        if ($user = get_userinfo()) {
            if (isset($user['username']))
                return $user['username'];
        }
        return "";
    }
}

/*
 * 获取用户姓名
 */
if (!function_exists('get_nickname')) {
    function get_nickname()
    {
        if ($user = get_userinfo()) {
            if (isset($user['nickname']))
                return $user['nickname'];
        }
        return "";
    }
}
/*
 * 获取用户gid
 */
if (!function_exists('get_gid')) {
    function get_gid()
    {
        if ($user = get_userinfo()) {
            if (isset($user['gid']))
                return intval($user['gid']);
        }
        return '';
    }
}

/*
 * 获取用户信息
 */
if (!function_exists('get_userinfo')) {
    function get_userinfo()
    {
        $user = CI()->auth->get_user_info();
        if (isset($user['uid']))
            return $user;
        return false;
    }
}

/*
 * type 参考:
 * 0 系统操作
 * 1 登录后台
 * 2
 *
 */
if (!function_exists('user_action_log')) {
    function user_action_log($uid = 0, $msg = "", $type = 0)
    {
        $data['uid'] = $uid;
        $data['site'] = $_SERVER["HTTP_HOST"];
        $data['ip'] = get_ip();
        R('ip/ip');
        $data['ip_msg'] = CI()->ip->ip2str(get_ip());
        $data['log_type'] = $type;
        $data['msg'] = $msg;
        $data['time'] = time();
        M('admin/sys_user_log_model')->insert($data);
    }
}


//下线用户:uid
if (!function_exists('user_offline')) {
    function user_offline($uid = 0)
    {
        if (intval($uid) > 0) {
            del_user_all_session($uid);
            user_action_log($uid, '被踢下线,操作员:' . get_sess('username') . '(' . get_sess('uid') . ')');
        }
    }
}

//下线用户:session_id
if (!function_exists('user_offline_by_session_id')) {
    function user_offline_by_session_id($session_key = '')
    {
        if (!empty($session_key)) {
            //开始清空session
            //在redis中的session key
            $session_key = C('redis_ci_session') . $session_key;

            if (del_cache($session_key)) {
                log_message('debug', "offline $session_key ok!");
            } else {
                log_message('debug', "offline $session_key fail!");
            }
        }
    }
}

/*
 * 设置用户来路信息
 */
if (!function_exists('set_user_refer')) {
    function set_user_refer()
    {
        //初始化数据
        $data = array();
        //注册域名
        $data['site'] = get_current_host();
        //来路域名
        if (isset($_SERVER['HTTP_REFERER']) && !str_exists($_SERVER['HTTP_REFERER'], get_top_domain())) {
            $data['refer'] = $_SERVER['HTTP_REFERER'];
        }
        //推荐人
        $uid = explode('?uid=', cur_page_url());
        if (count($uid) == 2 && intval($uid) > 0) {
            $data['p1'] = $uid[1];
        }
        $code = authcode(json_encode($data), 'ENCODE');
        set_cookie('user_refer', $code, 365 * 24 * 3600, get_top_domain());
    }
}

//获取用户来路信息
if (!function_exists('get_user_refer')) {
    function get_user_refer()
    {
        $data = array(
            'p1' => 0,
            'refer' => '',
            'site' => ''
        );


        $refer = get_cookie('user_refer');
        if (!$refer) {
            return $data;
        }
        $result = json_decode(authcode($refer, 'DECODE'), TRUE);

        if (!empty($result) && is_array($result)) {
            return $result;
        }
        return $data;
    }
}

