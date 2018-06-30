<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/29
 * Time: 20:04
 */

class Main extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->show('pc/main_view');

    }
}