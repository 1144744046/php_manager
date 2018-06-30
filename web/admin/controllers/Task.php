<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *
 *
 * 模拟Linux的 crontab 可以执行定时任务
 *
 *
 */

class Task extends Backend_Controller
{

    public function __construct()
    {
        parent::__construct();

        //模型名称
        $this->model_name = "admin/sys_task_model";
    }

    public function index()
    {
        $this->show('task_view');
    }

    public function time()
    {
        $pdata['id'] = intval($this->request('id', 0));
        $result = parent::search_by_pdata('admin/sys_task_time_model', $pdata);
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            //0为正确信息
            if (intval($result[0]) == 0) {
                echo json_encode($result[1]);
            } else {
                $this->render_ajax($result[0], $result[1]);
            }
        }
        exit;
    }

    public function clean_time()
    {
        $id = intval($this->request('id', 0));
        if (intval($id > 0)) {
            M('admin/sys_task_time_model')->delete(array('id' => $id));
            $this->render_ajax(0, '成功清空日志');
        } else {
            $this->render_ajax(1, '清空日志失败');
        }
    }

    public function excute()
    {
        $url = base_url("/admin/spider/excute?id=" . intval($this->request('id', 0)));
        $url = $url . "&token=" . token_url();
        redirect($url);
    }


}