<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| cache快捷函数
| -------------------------------------------------------------------
*/

//重新缓存所有系统参数
if (!function_exists('sys_config_cache_all')) {
    function sys_config_cache_all()
    {
        $result = M('admin/sys_config_model')->get_all(array(), '', '', 'name,value,is_del');
        foreach ($result as $v) {
            //逻辑删除变量
            if (intval($v['is_del']) == 1) {
                del_cache(sys_config_cache_key($v['name']));
            } else {
                set_cache(sys_config_cache_key($v['name']), $v['value']);
            }
            log_message('debug', "cache key: {$v['name']} => {$v['value']}}");
        }
    }
}

//获取某个分类下的参数
if (!function_exists('sys_config_get_menu_child')) {
    function sys_config_get_menu_childs($menuid = 0, $order = 'desc')
    {
        if ($order <> 'asc')
            $order = 'desc';
        $result = M('admin/sys_config_model')->get_all(array('menuid' => intval($menuid), 'is_del' => 0), '', "listorder $order");
        return $result;
    }
}

//获取系统参数
if (!function_exists('sys_config_get')) {
    function sys_config_get($name)
    {
        if (empty($name)) {
            echo '参数名不能为空';
            exit;
        }
        $cache_value = get_cache(sys_config_cache_key($name));
        //不存在缓存中
        if (empty($cache_value)) {
            $result = M('admin/sys_config_model')->get_one(array('name' => $name), '', 'value');
            if ($result) {
                set_cache(sys_config_cache_key($name), $result['value']);
                return $result['value'];
            } else {
                return '';
            }
        } else {
            return $cache_value;
        }
    }
}

//设置系统参数
if (!function_exists('sys_config_set')) {
    function sys_config_set($name = "", $value = "")
    {
        if (empty($name)) {
            echo '参数名不能为空';
            exit;
        }
        $result = M('admin/sys_config_model')->update(array('name' => $name), array('value' => $value));
        if ($result) {
            //重新获取缓存
            del_cache(sys_config_cache_key($name));
            set_cache(sys_config_cache_key($name), $value);
            return true;
        } else {
            return false;
        }
    }
}

//系统参数的cache key
if (!function_exists('sys_config_cache_key')) {
    function sys_config_cache_key($name)
    {
        if (empty($name)) {
            echo '参数名不能为空';
            exit;
        }
        return "sys_config:{$name}";
    }
}

