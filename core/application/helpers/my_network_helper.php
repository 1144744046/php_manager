<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * 优化file_get_contents函数、
 * 处理此函数导致服务器死掉问题
 * @param string $url
 * @param string $timeout 超时时间限制
 */
if (!function_exists('url_file_get_contents')) {
    function url_file_get_contents($url, $timeout = 3)
    {
        $ctx = stream_context_create(
            array(
                'http' => array(
                    // 设置一个超时时间，单位为秒
                    'timeout' => $timeout
                )
            )
        );
        return file_get_contents($url, false, $ctx);

    }
}


/**
 * 获取goto地址 排除 login register,logout
 * @param string $goto
 * @return string
 */
if (!function_exists('go_to_url')) {
    function go_to_url($goto = '')
    {
        if (!empty($goto)) {
            return $goto;
        } elseif (stripos(HTTP_REFERER, 'login') !== false) {
            return base_url();
        } elseif (stripos(HTTP_REFERER, 'register') !== false) {
            return base_url();
        } elseif (stripos(HTTP_REFERER, 'logout') !== false) {
            return base_url();
        } elseif (stripos(HTTP_REFERER, 'api') !== false) {
            return base_url();
        } else {
            return get_real_url(HTTP_REFERER);
        }
    }
}

/**
 * @param str $url 查询
 * $return str  定向后的url的真实url
 */
if (!function_exists('get_real_url')) {
    function get_real_url($url)
    {
        $header = get_headers($url, 1);
        if (strpos($header[0], '301') || strpos($header[0], '302')) {
            if (is_array($header['Location'])) {
                return $header['Location'][count($header['Location']) - 1];
            } else {
                return $header['Location'];
            }
        } else {
            return $url;
        }
    }
}

/**
 * 多线程执行，允许多个URL一起传进来
 * @param $urls  多个URL
 * @param $return  是否等待返回内容
 * @return string
 */
if (!function_exists('multi_thread')) {
    function multi_thread($url = '')
    {

        if (empty($url)) {
            echo 'multi_thread => url error : url = ' . $url;
            return '';
        }

        if (is_array($url)) {
            foreach ($url as $u) {
                multi_thread($u);
            }
        } else {
            $url = parse_url($url);
            $query = "";
            $port = "80";
            $host = $url['host'];

            if (isset($url['query']))
                $query = $url['path'] . "?" . $url['query'];
            else
                $query = $url['path'];

            if (isset($url['port']))
                $port = $url['port'];

            if ($url['scheme'] == 'https') {
                $host = "ssl://" . $url['host'];
                $port = 443;
            }

            $fp = fsockopen($host, $port, $errno, $errstr, 30);
            if ($fp) {
                $out = "GET $query HTTP/1.1\r\n";
                $out .= "Host: {$url['host']}\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fputs($fp, $out);
                /*
                fwrite($fp, $out);
                while (!feof($fp)) {
                    echo fgets($fp, 128);
                }
                */
                fclose($fp);
            }
        }
    }
}

//http post
//$data = file_get_contents_post("http://www.a.com/post/post.php", array('name'=>'test', 'email'=>'test@gmail.com'));
if (!function_exists('file_get_contents_post')) {
    function file_get_contents_post($url, $post = array(), $ip = "")
    {
        $options = array(
            'http' => array(
                'method' => 'POST',
                // 'content' => 'name=caiknife&email=caiknife@gmail.com',
                'header' => "Content-type: application/x-www-form-urlencoded",
                'content' => http_build_query($post),
            ),
        );

        $result = file_get_contents($url, false, stream_context_create($options));

        return $result;
    }
}

// 获取远程文件的大小(php模拟cdn专用)
if (!function_exists('remote_filesize')) {
    function remote_filesize($url, $host)
    {
        ob_start();
        $ch = curl_init();
        $useragent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $host));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        $ok = curl_exec($ch);
        curl_close($ch);
        $head = ob_get_contents();
        ob_end_clean();

        $regex = '/Content-Length:\s([0-9].+?)\s/';
        $count = preg_match($regex, $head, $matches);
        return isset($matches[1]) ? $matches[1] : 0;
    }
}
//文件下载(php模拟cdn专用)
if (!function_exists('curl_get_file_contents')) {
    function curl_get_file_contents($url, $post_data = array(), $host = '', $referer = '')
    {
        $useragent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        //主机
        if (!empty($host)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $host));
        }
        //post
        if (is_array($post_data) && count($post_data) > 0) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (isset($_SERVER['HTTP_COOKIE'])) {
            //$mycookie = str_replace("cnzz_rid", "test_rid", $_SERVER['HTTP_COOKIE']);
            curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
        }

        $data = curl_exec($ch);
        $ret = $data;
        if ($data === false) {
            echo curl_error($ch);
            exit;
        }else
        {
            return $data;
        }
    }
}