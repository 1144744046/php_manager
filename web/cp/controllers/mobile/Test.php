<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/24
 * Time: 17:50
 */

class Test extends Frontend_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index(){
        $this->show('mobile/test_view');
    }
    public function socket(){
        $this->show('mobile/socket_view');
    }
    public function connect(){
        $this->show('mobile/connect_view');
    }
}