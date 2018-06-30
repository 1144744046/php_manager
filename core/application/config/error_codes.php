<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 统一系统错误代码
|--------------------------------------------------------------------------
*/
$config['error_codes'] = array(
    0 => '登录成功',
    1 => '登录失败',
    2 => '未登录用户',
    3 => '权限受限',
    4 => '服务器错误',
    101 => '账号不能为空',
    102 => '账号不存在',
    103 => '密码不能为空',
    104 => '密码错误.',
    105 => '账号或密码错误',
    106 => '账号已被禁用',
    107 => '你的IP已被禁用',
    108 => '安全提问错',
    109 => '登录错误次数过多，账号已被禁用.请联系客服重置.',
    110 => '账号未审核.',
    111 => '账号不通过.',
    112 => '账号已冻结.',
    113 => '账号已停用.',
    114 => '你的IP不能登录此账号.',
    // 新增用户使用
    201 => '请求参数能为空',
    202 => '姓名或昵称包含不允许注册(敏感词)的词语',
    203 => '用户名长度为4~16个字符，支持字母和数字,不支持中文',
    204 => '用户名已经被注册',
    205 => '邮箱格式有误',
    206 => '邮箱已经被注册',
    207 => '密码须为6-12个同时包含字母或数字的组合',
    208 => '用户名不合法',
    209 => 'Email 不允许注册',
    210 => 'user_detail插入不成功',
    211 => '手机号码格示错误',
    212 => '微信号码格示错误',
    213 => 'QQ号码格示错误',
    //操作日志使用
    999 => '后台操作',
);