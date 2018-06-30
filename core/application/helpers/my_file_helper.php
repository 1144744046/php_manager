<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| 加载各个模块下common_helper.php
| 模块下的config文件中若存在和本文件相同的key值，会把本文件中设置的值覆盖掉
| -------------------------------------------------------------------------
*/
/*if(file_exists(APPPATH . 'modules/' . SITE . '/config/config.php')) {
    require_once APPPATH . 'modules/' . SITE . '/config/config.php';
}*/


//递归创建多级目录
function mkDirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkDirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

