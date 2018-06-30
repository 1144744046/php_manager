<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/25
 * Time: 16:23
 */

class Main extends Backend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function public_index()
    {
        $data=array();
        if(check_is_backend_admin(array('gid'=>$this->G['gid'],'uid'=>$this->G['uid'])))
            $data['report']='<script type="text/javascript" src="/static/easyui/plus/online.js"></script>';
        else
            $data['report']='';
        $this->show('main_view',$data);
    }
    // 侧边栏展开关闭状态记录
    public function ajax_side_status()
    {
        if($this->input->is_ajax_request()) {
            $status = $this->input->get('status');
            if($status == 'close') {
                set_cookie('side_status', 'close', 864000);
            } else {
                set_cookie('side_status', 'open', 864000);
            }
            exit;
        }
    }
}