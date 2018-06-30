<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/2/3
 * Time: 3:21
 */

class User extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_mobile_code(){
        echo get_sess("mobile_code");
    }
    //获取验证码
    public function get_code(){
        $mobile=$this->get("mobile");
        $yzm_code=$this->get("yzm_code");
        H("cp/my_yzm");
        if(check_yzm_by_code(strtolower($yzm_code))){
            if (!is_mobile_number($mobile)) {
                $this->render_ajax(1, '手机号码有误');
            }
            H("cp/my_sms");
            if(send_sms($mobile)){
                $this->render_ajax(0, '短信发送成功');
            }else{
                $this->render_ajax(1, '操作过于频繁,短信发送失败');
            }

        }else{
            $this->render_ajax(111, '图片验证码错误,请重新输入');
        }
    }


    //注册
    public function reg(){
        $pdata['username'] = $this->post('username');
        $pdata['password'] = $this->post('password');
        $pdata['mobile'] = $this->post('mobile');
        $pdata['nickname'] = $this->post('nickname');
        //短信验证码
        $code=$this->post("code");

        if(!is_password($pdata['password'])){
            $this->render_ajax(1, '密码须为6-16个同时包含字母或数字的组合');
        }
        if(!is_mobile_number($pdata['mobile'])){
            $this->render_ajax(1, '手机号码格示错误');
        }
        if(!is_username($pdata['username'])){
            $pdata['username']=rand_username(9);
        }

        //判断短信验证码
        H("cp/my_yzm");
        if(!check_mobile_yzm_by_code($code,$pdata['mobile'])){
            $this->render_ajax(1, '验证码错误');
        }
        $one = M('cp/common/user_model')->get_one(array('mobile' =>$pdata['mobile']), '', '*');
        if ($one) {
            $this->render_ajax(1, '手机号已经注册');
        }
        $result = (array)$this->auth->add_user($pdata);
        if ($result['ret'] == 0) {
            //$user = $this->auth->user;
            //设置 cookies
            //$this->auth->sign_user_info($user);
            $this->render_ajax(0, '注册成功');
        } else {
            $this->render_ajax($result['ret'], '添加用户失败.' . $result['msg']);
        }
    }
    //判断手机号是不是注册了
    public function is_mobile_reg(){
        $mobile=$this->get("mobile");
        $one = M('cp/common/user_model')->get_one(array('mobile' => $mobile), '', '*');
        if ($one) {
            $this->render_ajax(0, '手机号已经注册');
        }else{
            $this->render_ajax(1, '手机号未注册');
        }

    }
    //登录
    public function login(){

    }
}