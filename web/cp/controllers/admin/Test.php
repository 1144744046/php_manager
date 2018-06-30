<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/29
 * Time: 17:21
 */

class Test extends Backend_Controller
{
    public function __construct()
    {
        parent::__construct();
        //模型名称

    }

    public function index()
    {
        $this->show('admin/test_view');
    }

}