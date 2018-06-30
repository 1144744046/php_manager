<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * | -------------------------------------------------------------------
 * | 后台菜单管理
 * | -------------------------------------------------------------------
 */
class Config extends Backend_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->model_name = 'admin/sys_config_model';
    }

    public function index()
    {
        $this->show('config_view');
    }

    public function search()
    {
        $pdata = $this->prep_data(M($this->model_name)->table_name, FALSE, TRUE);
        $menuid = isset($pdata['menuid']) ? intval($pdata['menuid']) : 0;
        if ($menuid > 0) {
            //添加所有子栏目的id
            $child = $this->get_children_by_id($menuid);
            array_unshift($child, $pdata['menuid']);
            $pdata['in'] = array('menuid', $child);
            unset($pdata['menuid']);

            $result = parent::search_by_pdata($this->model_name, $pdata);
            //正确结果应为数组
            if (is_array($result) && count($result) == 2) {
                //0为正确信息
                if (intval($result[0]) == 0) {
                    echo json_encode($result[1]);
                } else {
                    $this->render_ajax($result[0], $result[1]);
                }
            }
            exit;
        }
    }

    public function save()
    {
        $result = parent::save_by_pdata();
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            //有数据库变动,更新系统缓存
            if ($result[0] == 0) {
                sys_config_cache_all();
            }
            $this->render_ajax($result[0], $result[1]);
        }
        exit;
    }


    /**
     * 删除接口
     * mname 模型名称
     */
    public function del()
    {
        $result = parent::del_by_pdata();
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            //有数据库变动,更新系统缓存
            if ($result[0] == 0) {
                sys_config_cache_all();
            }
            $this->render_ajax($result[0], $result[1]);
        }
        exit;
    }

    // 根据id获取所有子栏目id
    private function get_children_by_id($id)
    {
        $result = array();
        $menu = M('admin/sys_config_menu_model')->get_all(array('parentid' => $id), '', '', 'id,islast');
        foreach ($menu as $value) {
            if ($value['islast'] == 0) {
                $result[] = $value['id'];
                $child = $this->get_children_by_id($value['id']);
                $result = array_merge($result, $child);
            } else {
                $result[] = $value['id'];
            }
        }
        return $result;
    }
}