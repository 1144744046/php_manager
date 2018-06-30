<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/25
 * Time: 16:02
 */

if (!function_exists('crypt_get_key')) {
    function crypt_get_key()
    {
        $key = C('encryption_key');
        if (empty($key)) {
            $key = "hello";
            log_message('error', 'config encryption_key is empty.use: hello');
        }
        return $key;
    }
}

if (!function_exists('zip_gzdeflate')) {
    function zip_gzdeflate($str)
    {
        //压缩+加密
        R('Des', crypt_get_key());
        $crypt = $this->des;
        $str = gzdeflate($str, 9);
        $str = base64_encode($str);
        $str = $crypt->encrypt(($str));
        return $str;
    }
}

if (!function_exists('zip_gzencode')) {
    function zip_gzencode($str)
    {
        //压缩+加密
        R('Des', crypt_get_key());
        $crypt = $this->des;
        $str = gzencode($str, 9);
        $str = base64_encode($str);
        $str = $crypt->encrypt(($str));
        return $str;
    }
}
/*
 *  url 内部校验
 * 根据时间差值，有效期
 */
if (!function_exists('time_url_token')) {
    function token_url($operation = 'ENCODE', $data = "")
    {
        if ($operation == 'DECODE') {
            $data = rawurldecode($data);
            return intval(authcode($data));
        } else {
            $token = authcode(time(), "ENCODE");
            $token = rawurlencode($token);
            return $token;
        }
    }
}
/**
 * 字符串加密、解密函数 取自discuz
 * @param    string $txt 字符串
 * @param    string $operation ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param    string $key 密钥：数字、字母、下划线
 * @param    string $expiry 过期时间
 * @return    string
 */
if (!function_exists('authcode')) {
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : crypt_get_key());
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }
}


/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 *
 * @return array
 */
if (!function_exists('password')) {
    function password($password, $encrypt = '')
    {
        $pwd = array();
        $pwd['encrypt'] = $encrypt ? $encrypt : create_randomstr();
        $pwd['password'] = md5(md5(trim($password)) . $pwd['encrypt']);
        return $encrypt ? $pwd['password'] : $pwd;
    }
}

if (!function_exists('password_valid')) {
    function password_valid($candidate)
    {
        $r1 = '/[A-Z]/';  //uppercase
        $r2 = '/[a-z]/';  //lowercase
        $r3 = '/[0-9]/';  //numbers
        $r4 = '/[~!@#$%^&*()\-_=+{};:<,.>?]/';  // special char

        if (preg_match_all($r1, $candidate, $o) < 1) {
            return "密码必须包含至少一个大写字母，请返回修改！";
        }
        if (preg_match_all($r2, $candidate, $o) < 1) {
            return "密码必须包含至少一个小写字母，请返回修改！";
        }
        if (preg_match_all($r3, $candidate, $o) < 1) {
            return "密码必须包含至少一个数字，请返回修改！";
        }
        if (preg_match_all($r4, $candidate, $o) < 1) {
            return "密码必须包含至少一个特殊符号：[~!@#$%^&*()\-_=+{};:<,.>?]，请返回修改！";
        }
        if (strlen($candidate) < 8) {
            return "密码必须包含至少含有8个字符，请返回修改！";
        }
        return TRUE;
    }
}