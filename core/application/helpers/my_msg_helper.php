<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//发送站内消息
//uid 用户的id, 0 表示系统消息
//type 类型：0 => 消息中心、1 => 首页弹窗
function send_sys_msg($type = 0, $uid = 0, $title = '', $content = '', $operator = '')
{
    $data = array();
    $uid = intval($uid);
    if ($uid > 0) {
        $user = M('admin/sys_user_model')->get_one(array('uid' => $uid));
        if (!$user) {
            exit('user no found');
        }
    }
    $data['uid'] = $uid;
    $data['title'] = $title;
    $data['content'] = $content;
    $data['type'] = $type;
    $data['create_at'] = time();
    $data['update_at'] = time();
    $data['status'] = 0;
    $data['operator'] = $operator;
    return M('admin/sys_msg_model')->insert($data);
}

//根据用户名发送短消息
function send_sys_msg_by_username($type = 0, $username = '', $title = '', $content = '', $operator = '')
{
    if(!empty($username))
    {
        $user = M('admin/sys_user_model')->get_one(array('username' => $username));
        if ($user)
            return send_sys_msg($type, $user['uid'], $title, $content, $operator);
        else
            return false;
    }

    return send_sys_msg($type, 0, $title, $content, $operator);
}

//标记已读/未读  0为未读，1为已读
function change_sys_msg_status($mid = 0, $status = 1)
{
    $msg = get_sys_msg($mid);
    if (!empty($msg)) {
        if (intval($msg['uid']) == 0) {
            //处理系统消息

        } else {
            return M('admin/sys_msg_model')->update(array('mid' => $mid), array('status' => intval($status)));
        }

    }
}

//获取用户/系统消息列表和内容
function get_sys_msg($uid = 0, $page = 1, $rows = 10)
{
    //每次返回条数
    $page = max($page, 1);
    $limit = array($rows, $rows * ($page - 1));

    $msg = M('admin/sys_msg_model')->get_all(array('uid' => $uid), $limit, 'mid desc');
    if ($msg) {
        return $msg;
    }
    return false;
}


//获取单条消息内容
function get_sys_msg_content($mid = 0)
{
    $msg = M('admin/sys_msg_model')->get_one(array('mid' => $mid));
    if ($msg) {
        return $msg;
    }
    return false;
}
//获取一条弹窗消息
function get_pop_notice_msg(){
    $msg = M('admin/sys_msg_model')->get_one(array('type' => 1),'create_at desc','*');
    $msg['time']=date("Y:m:d H:m:s",$msg['create_at']);
    if ($msg) {
        return $msg;
    }
    return false;
}
