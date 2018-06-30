<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| session快捷函数
| -------------------------------------------------------------------
*/

// 取出
if (!function_exists('get_sess')) {
    function get_sess($id = NULL)
    {
        return CI()->session->userdata($id);
    }
}

// 存储
// 支持key-val方式
// 支持array(key=>val,key2=>val2)
if (!function_exists('set_sess')) {
    function set_sess($mix_data, $val = NULL)
    {
        CI()->session->set_userdata($mix_data, $val);
    }
}

// 删除
// 支持array(key=>val,key2=>val2)
if (!function_exists('del_sess')) {
    function del_sess($id)
    {
        CI()->session->unset_userdata($id);
    }
}

// 清空
if (!function_exists('clean_sess')) {
    function clean_sess()
    {
        CI()->session->sess_destroy();
    }
}

// session_id
if (!function_exists('sess_id')) {
    function sess_id()
    {
        return session_id();
    }
}

//更新session状态
if (!function_exists('update_user_session')) {
    function update_user_session($uid = 0)
    {
        if (intval($uid) == 0) {
            $uid = get_uid();
        }
        if (intval($uid) > 0) {
            $data = array();
            $data['sid'] = sess_id();
            $data['ip'] = get_ip();
            $data['site'] = SITE;//前端来路
            $data['t'] = time();
            if (isset($_SERVER['HTTP_USER_AGENT']))
                $data['ua'] = $_SERVER['HTTP_USER_AGENT'];//用户ua
            $data['cur_page'] = cur_page_url();//当前停留页面
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_top_domain()) === false) {
                $data['refer'] = $_SERVER['HTTP_REFERER'];//用户来路
            }

            //插入在线列表
            $key = C('redis_user_session_online_all');
            get_redis()->sAdd($key, $uid);

            //标识自身在线信息
            $key = C('redis_user_session_online_pre') . $uid;
            get_redis()->sAdd($key, sess_id());
            get_redis()->expire($key, C('sess_expiration'));

            get_redis()->set($key . ":sid:" . sess_id(), json_encode($data));
            get_redis()->expire($key . ":sid:" . sess_id(), C('sess_expiration'));
        }
    }
}

//注册session
if (!function_exists('get_user_session_list')) {
    function get_user_session_list($uid = 0)
    {
        $data = array();
        if (intval($uid) > 0) {
            $key = C('redis_user_session_online_pre') . $uid;
            $user_sessions = get_redis()->sMembers($key);
            foreach ($user_sessions as $session_id) {
                //如果session信息过期，去掉在线列表
                if (empty(get_redis()->get(C('redis_ci_session') . $session_id))) {
                    get_redis()->sRem($key, $session_id);
                    get_redis()->delete($key . ":sid:" . $session_id);
                    continue;
                }
                $session_data = get_redis()->get($key . ":sid:" . $session_id);
                if (!empty($session_data)) {
                    $session_data = (array)json_decode($session_data);
                    $data[] = $session_data;
                }
            }
        }
        return $data;
    }
}


//检查session是否在线
if (!function_exists('get_user_session_status')) {
    function check_user_session_status($uid = 0)
    {
        if (intval($uid) > 0) {
            $key = C('redis_user_session_online_pre') . $uid;
            $user_sessions = get_redis()->sMembers($key);
            foreach ($user_sessions as $session_id) {
                if (empty(get_redis()->get(C('redis_ci_session') . $session_id))) {
                    get_redis()->sRem($key, $session_id);
                    get_redis()->delete($key . ":sid:" . $session_id);
                    continue;
                }
                $session_data = get_redis()->get($key . ":sid:" . $session_id);
                if (!empty($session_data)) {
                    return true;
                }
            }
        }
        return false;
    }
}

//返回在线总数
if (!function_exists('get_user_online_num')) {
    function get_user_online_num()
    {
        $num = 0;
        $key = C('redis_user_session_online_all');
        foreach (get_redis()->sMembers($key) as $uid) {
            $uid_key = C('redis_user_session_online_pre') . $uid;
            if (get_redis()->exists($uid_key)) {
                $num++;
            } else {
                del_user_all_session($uid);
            }
        }
        return $num;
    }
}

//返回在线所有用户的uid
if (!function_exists('get_user_online')) {
    function get_user_online()
    {
        $key = C('redis_user_session_online_all');
        foreach (get_redis()->sMembers($key) as $uid) {
            $uid_key = C('redis_user_session_online_pre') . $uid;
            if (!get_redis()->exists($uid_key)) {
                del_user_all_session($uid);
            }
        }
        return get_redis()->sMembers($key);
    }
}

//注销session
if (!function_exists('del_user_all_session')) {
    function del_user_all_session($uid = 0)
    {
        $data = array();
        if (intval($uid) > 0) {
            $key = C('redis_user_session_online_pre') . $uid;
            $user_sessions = get_redis()->sMembers($key);
            //下线所有客户端
            foreach ($user_sessions as $session_id) {
                //下线每个客户端
                get_redis()->del($key . ":sid:" . $session_id);
                get_redis()->del(C('redis_ci_session') . $session_id);
            }
            //删除标识
            get_redis()->del($key);
            //在线列表移除
            get_redis()->sRem(C('redis_user_session_online_all'), $uid);
        }
        return $data;
    }
}