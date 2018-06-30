<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/29
 * Time: 22:29
 */

class User_center extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->show('pc/main_view');
    }
    public function register()
    {
        $this->show('pc/user/register_view');
    }
}