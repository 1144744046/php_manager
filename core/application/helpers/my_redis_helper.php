<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 本地redis
 */
if (!function_exists('local_redis')) {
    function local_redis()
    {
        $host = "127.0.0.1";
        $port = "6379";
        try {
            $redis = new Redis();
            $ret = $redis->pconnect($host, $port);
            if ($ret === false) {
                die($redis->getLastError());
            }
            if ($ret === false) {
                die($redis->getLastError());
            }

            return $redis;
        } catch (RedisException $e) {
            die("Uncaught exception " . $e->getMessage());
        }
    }
}

/*
 * 获取系统redis
 */
if (!function_exists('get_redis')) {
    function get_redis()
    {
        return local_redis();
    }
}

/*
 * 关闭redis
 */
if (!function_exists('close_redis')) {
    function close_redis()
    {
        $redis = get_redis();
        if ($redis) {
            $redis->close();
        }
    }
}
