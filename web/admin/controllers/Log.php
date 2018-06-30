<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 后台用户基本管理
|--------------------------------------------------------------------------
|
*/

class Log extends Backend_Controller
{
    public function __construct()
    {
        parent::__construct();

        //模型名称
        $this->model_name = "admin/sys_log_model";
    }

    public function index()
    {
        $this->show('log_view');
    }

    /*
     * 查询接口
     */
    public function search()
    {
        //获取表单中的数据
        $pdata = $this->prep_data(M($this->model_name)->table_name, FALSE);

        if(!empty($pdata['data']))
        {
            $pdata['data'] = utf8_str_to_unicode($pdata['data']);
        }
        //自定义条件
        //超级管理员可以查所有记录，其他人只能查看自己的记录
        if (!$this->is_founder) {
            $pdata['username'] = $this->G['username'];
        }

        $result = parent::search_by_pdata($this->model_name, $pdata);
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

    /**
     * 清空数据库接口
     */
    public function clean()
    {
        if (M($this->model_name)->truncate_table()) {
            $this->oper_log();
            $this->render_ajax(0, '成功清空所有日志');
        } else {
            $this->render_ajax(1, '清空日志失败');
        }
    }
}
