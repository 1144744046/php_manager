<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/24
 * Time: 18:56
 */

class Frontend_Controller extends Base_Controller
{
    public function __construct(){
        parent::__construct();
    }

    protected function show($template, $data = array())
    {
        $path = $this->view_style . "/";
        $this->load->view($path . $template, $data);
    }
}