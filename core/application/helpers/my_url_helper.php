<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2017/10/5
 * Time: 9:18
 */
if(!function_exists('base_url')){
    function base_url($uri=""){
        $CI=&get_instance();
        return $CI->config->base_url($uri);
    }
}

if(!function_exists('static_url')){
    function static_url($uri=""){
        $base=base_url('static').'/';
        return $base.ltrim($uri,'/');
    }
}
/*
 * 判断是否为ajax请求
 */
if (!function_exists('is_ajax')) {
    function is_ajax()
    {
        $url = current_url();
        if (strpos($url, 'ajax_') > 0) {
            return TRUE;
        }
        return CI()->input->is_ajax_request();
    }
}

/*
 * 请求类型是否是POST
 */
if (!function_exists('is_post')) {
    function is_post()
    {
        return (CI()->input->server('REQUEST_METHOD') === 'POST');
    }
}

/*
 * 请求类型是否是GET
 */
if (!function_exists('is_get')) {
    function is_get()
    {
        return (CI()->input->server('REQUEST_METHOD') === 'GET');
    }
}
/**
 * 获取当前url地址
 * @return string
 */
if (!function_exists('cur_page_url')) {
    function cur_page_url()
    {
        $pageURL = 'http';
        if (CI()->input->server('HTTPS') == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if (CI()->input->server('SERVER_PORT') != "80") {
            $pageURL .= CI()->input->server("SERVER_NAME") . ":" . CI()->input->server("SERVER_PORT") . CI()->input->server("REQUEST_URI");
        } else {
            $pageURL .= CI()->input->server("SERVER_NAME") . CI()->input->server("REQUEST_URI");
        }
        return $pageURL;
    }
}
if (!function_exists('get_top_domain')) {
// 获取顶级域名
    function get_top_domain($url = '')
    {
        $url = empty($url) ? $_SERVER['HTTP_HOST'] : '';
        $host = strtolower($url);
        if (strpos($host, '/') !== false) {
            $parse = @parse_url($host);
            $host = $parse ['host'];
        }
        $topleveldomaindb = array('com.cn', 'com', 'cn', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me', 'top', 'io', 'vc', 'top', 'xyz', 'shop', 'int', 'la', 'hk', 'bb');
        $str = '';
        foreach ($topleveldomaindb as $v) {
            $str .= ($str ? '|' : '') . $v;
        }

        $matchstr = "[^.]+.(?:(" . $str . ")|w{2}|((" . $str . ").w{2}))$";
        if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
            $domain = $matchs ['0'];
        } else {
            $domain = $host;
        }
        return $domain;
    }
}