<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * |--------------------------------------------------------------------------
 * | 登录管理后台
 * |--------------------------------------------------------------------------
 */
class Robot extends Base_Controller
{
    public function __construct()
    {

        parent::__construct();
        //模型名称
        $this->model_name = "admin/sys_task_model";
    }

    public function index()
    {
        //M('admin/sys_log_model')
    }

    /*
    # For details see man 4 crontabs

    # Example of job definition:
    # .---------------- minute (0 - 59)
    # |  .------------- hour (0 - 23)
    # |  |  .---------- day of month (1 - 31)
    # |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...
    # |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
    # |  |  |  |  |
    # *  *  *  *  * url to be executed

     * 格式
     * $task[] = "* * * * * url"
     * $task[] = "分 时 天 月 年 执行的URL"
     */
    //每分钟执行为 * * * * * url*
    //每3分钟执行为 */3 * * * * url
    //每小时的第1分钟执行为 1 * * * * url
    //每3小时的第1分钟执行为 1 */3 * * * url
    //每天的凌晨1点0分执行为  0 1 * * * url
    //每天的凌晨1点的每一分钟都执行为  * 1 * * * url
    //每月1号的凌晨1点0分执行为 0 1 1 * * url
    public function excute_task()
    {
        $task = M('admin/sys_task_model')->get_all();
        if ($task) {
            foreach ($task as $tk) {
                $t = explode(" ", trim($tk['cycle']));

                if (count($t) == 5) {
                    //分钟
                    $i = $this->get_value($t[0], date('i'));
                    //小时
                    $H = $this->get_value($t[1], date('H'));
                    //日期
                    $d = $this->get_value($t[2], date('d'));
                    //月份
                    $m = $this->get_value($t[3], date('m'));
                    //年份
                    $Y = $this->get_value($t[4], date('Y'));

                    //mktime(hour,minute,second,month,day,year,is_dst)
                    $do_time = mktime($H, $i, 0, $m, $d, $Y);
                    $now = mktime(date('H'), date('i'), 0, date('m'), date('d'), date('Y'));

                    if ($do_time == $now) {
                        $url = base_url("/admin/spider/excute?id=" . $tk['id']);

                        //修正url get 传值存在 + 问题
                        $url = $url . "&token=" . token_url();
                        @multi_thread($url);
                        echo "task: {$tk['id']}->{$tk['name']} 执行成功.执行周期:{{$tk['cycle']}}<br />\n";
                    } else {
                        echo "task: {$tk['id']}->{$tk['name']} 未在执行周期:{{$tk['cycle']}}<br />\n";
                    }
                }

            }
        }
    }

    // 判断是否是  */5 这样的 每隔 N * 格式
    private function get_value($expr, $value)
    {
        if ($expr == '*')
            //如果是通配符，就返回当前时间
            return $value;

        if (strpos($expr, '/') !== FALSE) {
            $e = explode('/', $expr);
            if (sizeof($e) !== 2) {
                return $expr;
            }
            if (!is_numeric($e[1])) {
                return $expr;
            }
            //如果可以整除，就返回当前时间。如果不是，返回一个错误时间
            if ($value % $e[1] === 0) {
                return $value;
            } else {
                return $value - 1;
            }

        }
        //无法匹配,返回默认值
        return $expr;
    }


    public function excute()
    {
        set_time_limit(1200);

        $id = intval($this->request('id', 0));
        $token = $this->request('token');
        $token = token_url("DECODE", $token);
        if (time() - $token > 60) {
            echo "非法token.\n<br />" . cur_page_url();
            exit;
        }
        $pdata['id'] = $id;
        $task = M($this->model_name)->get_one($pdata);
        if ($task) {
            $url = parse_url($task['url']);
            /*
             array(5) { ["scheme"]=> string(5) "https" ["host"]=> string(13) "www.baidu.com" ["port"]=> int(88) ["path"]=> string(6) "/path/" ["query"]=> string(3) "a=2" }
            */
            $query = $url['scheme'] . "://";
            if (isset($task['ip']) && !empty($task['ip'])) {
                $query .= $task['ip'];
            } else {
                $query .= $url['host'];
            }
            if (isset($url['port']) && !empty($url['port'])) {
                $query .= ":" . $url['port'];
            }
            if (isset($url['path']) && !empty($url['path'])) {
                $query .= $url['path'];
            }
            if (isset($url['query']) && !empty($url['query'])) {
                $query .= "?" . $url['query'];
                if (isset($task['get_parm']) && !empty($task['get_parm'])) {
                    $query .= "&" . $task['get_parm'];
                }
            } else {
                if (isset($task['get_parm']) && !empty($task['get_parm'])) {
                    $task['get_parm'] = str_replace("#token#", token_url(), $task['get_parm']);
                    $query .= "?" . $task['get_parm'];
                }
            }

            $post_data = array();
            if (isset($task['post_parm']) && !empty($task['post_parm'])) {
                parse_str($task['post_parm'], $post_data);
            }

            @$result = curl_get_file_contents($query, $post_data, $url['host']);

            //执行日志
            M('admin/sys_task_time_model')->insert(array(
                'id' => $id,
                'time' => time(),
                'result' => substr(htmlspecialchars($result), 0, 5120)
            ));

            M('admin/sys_task_model')->update(
                array('id' => $id),
                array(
                    'last_execute_time' => time()
                ));

            echo $result;
        } else {
            echo "任务ID:$id 不存在";
        }
    }
}