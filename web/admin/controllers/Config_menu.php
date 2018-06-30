<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * | -------------------------------------------------------------------
 * | 后台菜单管理
 * | -------------------------------------------------------------------
 */
class Config_menu extends Backend_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->model_name = 'admin/sys_config_menu_model';
    }

    public function index()
    {
        $this->show('config_menu_view');
    }

    public function get_all_menu()
    {
        $result = M('admin/sys_config_menu_model')->get_menus(0, TRUE);
        echo json_encode($result);
    }

    public function save()
    {
        $pdata = $this->post();
        if (is_post() && intval($this->post('id') > 0)) {
            $pdata['listorder'] = !empty($pdata['listorder']) && intval($pdata['listorder']) ? intval($pdata['listorder']) : 50;
            M($this->model_name)->update(array('id' => $pdata['parentid']), array('islast' => 0));
            M($this->model_name)->update(array('id' => $pdata['id']), $pdata);
            $this->render_ajax(0, '修改成功', array('id' => $this->post('id')));
        } else if (is_post() && !intval($this->post('id'))) {
            $pdata['islast'] = 1;
            unset($pdata['id']);
            $pdata['listorder'] = !empty($pdata['listorder']) && intval($pdata['listorder']) ? intval($pdata['listorder']) : 50;
            M($this->model_name)->update(array('id' => $pdata['parentid']), array('islast' => 0));
            $id = M($this->model_name)->insert($pdata, TRUE);
            $this->render_ajax(0, '新增成功', array('id' => $id));
        } else {
            $this->render_ajax(1, '保存失败');
        }
    }

    /**
     * 菜单删除
     *
     */
    public function del()
    {
        $id = intval($this->post('id'));
        if (is_post() && intval($this->post('id')) > 0) {

            $one = M($this->model_name)->get_one(array('parentid' => $id));
            if ($one) {
                $this->render_ajax(1, '删除失败,请先删除子菜单. ');
            }
            M($this->model_name)->delete(array('id' => $id));
            $this->render_ajax(0, '删除成功');
        } else {
            $this->render_ajax(1, '删除失败');
        }
    }
}