<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/25
 * Time: 16:06
 */
/**
 * 生成随机字符串
 * @param string $lenth 长度
 *
 * @return string 字符串
 */
if (!function_exists('create_randomstr')) {
    function create_randomstr($lenth = 6)
    {
        return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
    }
}
/**
 * 产生随机字符串
 * @param    int $length 输出长度
 * @param    string $chars 可选的 ，默认为 0123456789
 * @return   string     字符串
 */
if (!function_exists('random')) {
    function random($length, $chars = '0123456789')
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}
if (!function_exists('json2code')) {
    function json2code($data)
    {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return '"' . $data . '"';
            case 'string':
                return '"' . addcslashes($data, "\r\n\t\"\\") . '"';
            case 'object':
                $data = get_object_vars($data);
                return $data;
            case 'array':
                $count = 0;
                $indexed = array();
                $associative = array();
                foreach ($data as $key => $value) {
                    if ($count !== NULL && (gettype($key) !== 'integer' || $count++ !== $key)) {
                        $count = NULL;
                    }
                    $one = json2code($value);
                    $indexed[] = $one;
                    $associative[] = json2code($key) . ':' . $one;
                }
                if ($count !== NULL) {
                    return '[' . implode(',', $indexed) . ']';
                } else {
                    return '{' . implode(',', $associative) . '}';
                }
            default:
                return '';
        }
    }
}

/**
 * 查询字符是否存在于某字符串
 * @param $haystack 字符串
 * @param $needle   要查找的字符
 * @return bool
 */
if (!function_exists('str_exists')) {
    function str_exists($haystack, $needle)
    {
        return !(strpos($haystack, $needle) === FALSE);
    }
}

