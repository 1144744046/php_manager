<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| cache快捷函数
| -------------------------------------------------------------------
*/
// 如果redis存储在优先使用redis
if (!function_exists('my_cache')) {
    function my_cache()
    {
        CI()->load->driver('cache');
        $redis = CI()->cache->redis->is_supported();
        if ($redis) {
            return CI()->cache->redis;
        } else {
            log_message('error','php can not support redis');
        }
    }
}

/*
 * 获取缓存
 */
if (!function_exists('get_cache')) {
    function get_cache($name = "")
    {
        if (empty($name)) {
            echo "cache id is empty";
        } else {
            return my_cache()->get($name);
        }
    }
}

/*
 * 设置缓存
 */

if (!function_exists('set_cache')) {
    function set_cache($name = "", $value = "", $ttl = 3600 * 24 * 30)
    {
        if (empty($name)) {
            echo "cache id is empty";
        } else {
            return my_cache()->save($name, $value, $ttl);
        }
    }
}


/*
 * 删除缓存
 */
if (!function_exists('del_cache')) {
    function del_cache($name = "")
    {
        if (empty($name)) {
            echo "cache id is empty";
        } else {
            return my_cache()->delete($name);
        }
    }
}
