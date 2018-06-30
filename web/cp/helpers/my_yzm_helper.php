<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 获取验证码的html代码
 */
if (!function_exists('get_yzm_html')) {
    function get_yzm_html()
    {
        $code = "<img id='regValidImg' width='80' height='40' src='/cp/api/yzm_api/get_yzm?" . time() . "' onclick=\"this.src='/cp/api/yzm_api/get_yzm?t='+new Date().getTime()\" />";
        return $code;
    }
}
/*
 * 获取验证码的图片url
 */
if (!function_exists('get_yzm_img_url')) {
    function get_yzm_img_url()
    {
        $url = "/cp/api/yzm_api/get_yzm?" . time();
        return $url;
    }
}
/*
 * 获取验证码的html代码
 */
if (!function_exists('get_yzm_code')) {
    function get_yzm_code()
    {
        return get_sess("yzm_code");
    }
}
/*
 * 校验验证码
 */
if (!function_exists('check_yzm_by_code')) {
    function check_yzm_by_code($code = "")
    {
//        echo $code;
        if (get_sess("yzm_code") == $code)
            return true;
        return false;
    }

}
/*
 * 校验验证码
 */
if (!function_exists('check_mobile_yzm_by_code')) {
    function check_mobile_yzm_by_code($code = "",$mobile)
    {
//        echo $code;
        if ((get_sess("mobile_code") == $code) && (get_sess("mobile") == $mobile))
            return true;
        return false;
    }
}