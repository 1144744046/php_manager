<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2017/10/5
 * Time: 9:18
 */
/**
 * 打印
 * 使用说明：
 * 不传参数打印回溯，帮助了解代码运行流程
 * 1个参数打印基本概况，常规调试
 * 2个参数打印详情数据，深入了解类型
 * 3个参数打印PHP可以使用的数组，方便数据库测试数据使用
 * @author chimero lau
 */
if (!function_exists('myprint')) {
    function myprint()
    {
        $N = func_num_args();
        $A = func_get_args();
        echo '<pre>';
        $N == 0 ? print_r(debug_backtrace()) : ($N == 1 ? print_r($A[0]) : ($N == 2 ? var_dump($A[0]) : var_export($A[0])));
        exit;
    }
}

/**
 * 获取CI核心对象
 *
 * @return obj
 */
if (!function_exists('CI')) {
    function CI()
    {
        $CI =& get_instance();
        return $CI;
    }
}

/*
| -------------------------------------------------------------------
| C,H,R,L,M为扩展系统内核的五个简短函数
| -------------------------------------------------------------------
*/
/**
 * 加载配置文件
 * @param        $name
 * @param string $file
 *
 * @return string
 */
if (!function_exists('C')) {
    function C($name, $file = '')
    {

        if (empty($file)) {
            return CI()->config->item($name);
        } else {
            CI()->config->load($file);
            return CI()->config->item($name);
        }
    }
}

/**
 * 加载helper函数库
 *
 * @param string $file
 */
if (!function_exists('H')) {
    function H($file = '')
    {
        return CI()->load->helper($file);
    }
}

/**
 * 加载library类库
 *
 * @param string $class
 * @param array $config
 * @param string $string
 */
if (!function_exists('R')) {
    function R($class, $config = array(), $obj_name = NULL)
    {
        if ($config != NULL && !is_array($config)) {
            $config = array($config);
        }
        if ($obj_name != NULL) {
            CI()->load->library($class, $config, $obj_name);
        } else {
            CI()->load->library($class, $config);
        }
    }
}

/**
 * 加载语言包
 * @return string
 */
if (!function_exists('L')) {
    function L()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $out_data = CI()->lang->line($arg_list[0]);
        if ($out_data) {
            if ($numargs > 1) {
                $app = '$out_data = sprintf( $out_data ';
                for ($i = 1; $i < $numargs; $i++) {
                    $app = $app . ",\$arg_list[$i]";
                }
                $app = $app . ");";
                eval($app);
            }
            return $out_data;
        } else {
            return '';
        }

    }
}

/**
 * 延迟按需加载模型
 *
 * @param $model_name  string 模型全名
 * @param $model_alias  string 模型别名，默认空
 * @return mixed
 */
if (!function_exists('M')) {
    function M($model_name, $model_alias = NULL)
    {
        static $model_container = array();
        $model_alias || $model_alias = '__' . str_replace('/', '_', $model_name);
        if (!isset($model_container[$model_alias])) {
            CI()->load->model($model_name, $model_alias);
            $model_container[$model_alias] = CI()->$model_alias;
        }
        return $model_container[$model_alias];
    }
}

/**
 * 获取用户头像
 * @param string $avatar 头像存储地址
 * @param bool $sizetype 头像尺寸
 * @return mixed
 */
if (!function_exists('get_avatar')) {
    function get_avatar($uid, $sizetype = 'middle')
    {
        return '/user/avatar/' . $uid . '/' . $sizetype;
    }
}

/**
 * 根据时间散列存储图片
 * @param string $attach_root 图片存储根目录
 * @param bool $return_array 是否用数组形式返回目录
 * @return mixed
 */
if (!function_exists('get_img_dir')) {
    function get_img_dir($attach_root = 'image')
    {
        $target_dir = root_path() . '/upload/' . $attach_root . '/';

        if (!is_dir($target_dir)) {
            return FALSE;
        }

        $dirs = array(date('Ym'), date('dh'));

        foreach ($dirs as $dir) {
            $target_dir .= $dir . '/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777);
            }
        }

        return array(
            'root_dir' => $target_dir,
            'dir' => implode('/', $dirs)
        );

    }
}

/**
 * 记录日志
 * @param int $level 日志等级
 * @param string $msg 自定义日志消息
 *
 * @script_file string $msg 日志发生脚本文件地址
 * @return bool
 */
/*
|--------------------------------------------------------------------------
| // level说明
| 1 => 'EMERG', // Emergency: 系统不可用
| 2 => 'ALERT', // Alert: 报警
| 3 => 'CRIT', // Critical: 紧要
| 4 => 'ERR', // Error: 错误
| 5 => 'WARN', // Warning: 警告
| 6 => 'NOTICE', // Notice: 通知
| 7 => 'INFO', // Informational: 一般信息
| 8 => 'DEBUG' // Debug: 调试消息
|--------------------------------------------------------------------------
|
*/
if (!function_exists('log_msg')) {
    function log_msg($level, $msg, $script_file = '', $locate_line = 0)
    {
        CI()->load->driver('log');
        CI()->log->write($level, $msg, $script_file, $locate_line);
    }
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址');
 *
 * @param string $msg 提示信息
 * @param mixed (string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
if (!function_exists('showmessage')) {
    function showmessage($msg, $url_forward = '', $ms = 3000)
    {
        $data['url_forward'] = !empty($url_forward) ? $url_forward : base_url();
        $data['msg'] = $msg;
        $data['ms'] = $ms;
        CI()->load->view('admin/default/common/message', $data);
        exit;
    }
}

/**
 * 获取文件后缀名
 * @param string $file 文件名
 */
if (!function_exists('get_ext')) {
    function get_ext($file)
    {
        return strtolower(array_pop(explode('.', $file)));
    }
}

/*
 * 时间格式化
 * 格式化出人性化的时间
 */
if (!function_exists('pretty_time')) {
    function pretty_time($time, $pretty = TRUE)
    {
        if (!is_numeric($time)) {
            return $time;
        }
        // 时间间隔
        $diff = time() - $time;
        // 格式化
        if ($diff < 60) {
            return $diff . '秒前';
        }
        if ($diff >= 60 && $diff < 60 * 30) {
            return intval($diff / 60) . '分钟前';
        }
        if ($diff >= 60 * 30 && date('Ymd', $time) == date('Ymd', time())) {
            return '今天 ' . date('H:i:s', $time);
        }

        if ($diff > 60 * 30 && date('Ymd', $time) == date('Ymd', strtotime('-1 day', time()))) {
            return '昨天 ' . date('H:i:s', $time);
        }

        if ($diff > 60 * 30 && date('Y', $time) == date('Y', time())) {
            return date('m月d日 H:i:s', $time);
        }

        return date('Y年m月d日 H:i:s', $time);
    }
}

/*
 * 时间格式化
 * 格式化出人性化的时间
 */
if (!function_exists('pretty_search')) {
    function pretty_search($content, $keywords)
    {
        if (empty($keywords)) {
            return $content;
        }

        if (is_array($keywords)) {
            foreach ($keywords as $keyword) {
                $content = str_replace($keyword, '<font color="red">' . $keyword . '</font>', $content);
            }

            return $content;
        }

        $content = str_replace($keywords, '<font color="red">' . $keywords . '</font>', $content);

        return $content;
    }
}

/**
 * 判断用户是否为创始人
 * @param $user 用户信息
 *
 * @return bool
 */
if (!function_exists('checkfounder')) {
    function checkfounder($user)
    {
        if ($user['uid'] == 0 || !preg_match('/^[1-9]{1}\d*$/', $user['uid'])) {
            return false;
        }

        $founders = array_filter(explode(',', str_replace(' ', '', C('admin_founder'))));
        if (in_array($user['uid'], $founders)) {
            return true;
        }

        return false;
    }
}
/**
 * 判断用户是否为后台管理员
 * @param $user 用户信息
 *
 * @return bool
 */
if (!function_exists('check_is_backend_admin')) {
    function check_is_backend_admin($user)
    {
        if ($user['gid'] == 0 || !preg_match('/^[1-9]{1}\d*$/', $user['uid'])) {
            return false;
        }

        $one = M('admin/sys_group_model')->get_one(array('gid' => $user['gid']), '', 'is_backend_admin');
        if ($one && $one['is_backend_admin'] == 1) {
            return true;
        }

        return false;
    }
}
/**
 * 加载模版函数
 * @param $module
 * @param $dir
 * @param string $template
 * @param string $style
 * @return string
 */
function template($module, $dir, $template = 'index', $style = 'default')
{
    if (empty($template)) return '';
    $style = !empty($style) ? $style . '/' : '';
    $dir = !empty($dir) ? $dir . '/' : '';
    if (empty($module)) {

        if (empty($dir)) {
            $require = APPPATH . 'views/' . $style . $template . '.php';
        } else {
            $require = APPPATH . 'views/' . $style . $dir . $template . '.php';
        }
    } else {
        if (!empty($dir) && $dir == 'common/') {
            $require = APPPATH . 'views/' . $module . '/common/' . $template . '.php';
        } else if (!empty($dir) && $dir != 'common/') {
            $require = APPPATH . 'views/' . $module . '/' . $style . $dir . $template . '.php';
        } else {
            $require = APPPATH . 'views/' . $module . '/' . $style . $template . '.php';
        }
    }
    return $require;
}


/**
 * 判断字符串是否有效的url地址
 *
 * @param string $url
 */
if (!function_exists('is_available_url')) {
    function is_available_url($url = '')
    {
        return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i', $url);

    }
}

/**
 * 判断是否有效的EMAIL地址
 * @param string $email
 * @return bool
 */
if (!function_exists('is_email')) {
    function is_email($email = '')
    {
        return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
    }
}

/**
 * 判断是否有效的ip地址
 * @param string $ip
 * @return bool
 */
if (!function_exists('is_ip_address')) {
    function is_ip_address($ip = '')
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}

/**
 * 判断是否是移动设备
 * $decive 设备类型 iphone，ipad，。。。
 *
 */
if (!function_exists('is_mobile_agent')) {
    function is_mobile_agent($device = NULL)
    {
        R('user_agent');
        return CI()->agent->is_mobile($device);
    }
}

/**
 * 统计代码程序执行的时间
 * G('start')
 * code......
 * G('end') //如果没有这个则代码结束标志在echo G('start','end')
 * echo G('start','end')
 * @param string 开始时间标志
 * @param string 结束时间标志
 * @param int    保留小数点的位数
 * @return float  微秒数
 */
if (!function_exists('G')) {
    function G($start = '', $end = '', $dec = 6)
    {
        static $_info = array();

        if (!empty($end)) {
            if (!isset($_info[$end])) {
                $_info[$end] = microtime(true);
            }
        } else {
            $_info[$start] = microtime(true);
        }
        $time = number_format($_info[$end] - $_info[$start], $dec);
        return $time;
    }


}

//是否是手机号码
function is_mobile_number($phone)
{
    if (strlen($phone) != 11 || !preg_match('/^1[3|4|5|6|7|8|9][0-9]\d{4,8}$/', $phone)) {
        return false;
    } else {
        return true;
    }
}

//是否是qq
function is_qq($qq)
{
    if (strlen($qq) < 5 || strlen($qq) > 15 || !is_numeric($qq)) {
        return false;
    } else {
        return true;
    }
}

//是否是微信
function is_weixin($weixin)
{
    if (strlen($weixin) < 5 || str_length($weixin) > 20) {
        return false;
    } else {
        return true;
    }
}

//是否是用户名/4-16位的字母或数字的组合
function is_username($str)
{
    // 检测用户名是否合法//只能是数字和字母的组合
    if (!preg_match("/^[0-9a-zA-Z]{3,12}$/", $str)) {
        return false;
    }
    $ulen = strlen($str);
    if ($ulen > 16 || $ulen < 4) {
        return false;
    }
    return true;
}

//是否是密码/6-16位的字母和数字的组合
function is_password($str)
{
    // /只能是数字和字母的组合
    if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $str)) {
        return false;
    }
    $ulen = strlen($str);
    if ($ulen > 16 || $ulen < 6) {
        return false;
    }
    return true;
}

//block 模块

/**
 * 根据模块id获取 模块 html 代码
 * @param $bid
 * @return string
 */
function get_html_block_by_id($bid)
{
    $path = 'cms/block/block_' . $bid;
    if (!$data = get_cache($path)) {
        $r = M('ads/block_model')->get_one(array('bid' => $bid), 'code');
        $data = $r;
        if (!empty($r)) {
            set_cache($path, $r, 86400);
        }
    }
    if (isset($data['code'])) {
        return stripslashes($data['code']);
    }
    return '';
}

/**
 * 跟据标识获取 模块下内容
 * @param $sign
 * @param string $row
 * @return array
 */
function get_block_by_sign($sign, $row = '')
{
    if (empty($sign)) return array();
    if (stripos($row, ',') === false && intval($row)) {
        $limit = array($row, 0);
    } else if (stripos($row, ',') !== false) {
        list($start, $end) = explode(',', $row);
        $limit = array($end, $start);
    } else {
        $limit = '';
    }
    $r = M('ads/block_model')->get_one(array('sign' => $sign), 'bid');
    if (empty($r)) return array();
    $result = M('ads/block_content_model')->get_all(array('bid' => $r['bid'], 'status' => 1), $limit, 'listorder asc,id desc', '*');

    return $result;
}

/**
 * 根据标识获取模块的 html 代码
 * @param $sign
 * @return string
 */
function get_html_block_by_sign($sign)
{
    if (empty($sign)) return '';
    $r = M('ads/block_model')->get_one(array('sign' => $sign), 'code');
    if (isset($r['code'])) {
        return stripslashes($r['code']);
    }
    return '';
}

/**
 * 获取请求ip
 *
 * @return string
 */
if (!function_exists('get_ip')) {
    function get_ip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    }
}


/*
 *浏览器缓存控制
 *
 */
if (!function_exists('broswer_cache')) {
    function broswer_cache($seconds_to_cache = "3600")
    {
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Pragma: cache");
        header("Expires: $ts");
        header("Cache-Control: max-age=$seconds_to_cache");
    }
}


//用户操作信息编码转换
if (!function_exists('user_code_to_msg')) {
    function user_code_to_msg($code = 3)
    {
        $error_codes = C('error_codes', 'error_codes');
        return isset($error_codes[$code]) ? $error_codes[$code] : $error_codes[3];
    }
}
if (!function_exists('rand_username')) {
    function rand_username($param)
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for ($i = 0; $i < $param; $i++) {
            $key .= $str{mt_rand(0, 32)};    //生成php随机数
        }
        return $key;
    }
}


