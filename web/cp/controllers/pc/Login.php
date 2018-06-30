<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/29
 * Time: 22:11
 */

class Login extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->show('pc/login_view');
    }
}