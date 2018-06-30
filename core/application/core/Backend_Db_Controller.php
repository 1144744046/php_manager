<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * | -------------------------------------------------------------------
 * | 数据库控制器
 * | -------------------------------------------------------------------
 */
class Backend_Db_Controller extends Base_Controller
{
    //管理操作日志
    public $sys_user_log_msg = "";

    public function __construct()
    {
        parent::__construct();
        // 填充用户登录状态
        if (is_array(get_userinfo()))
            $this->G = array_merge($this->G, get_userinfo());
    }

    /*
     * 查询接口
     * mname 模型名称
     * pdata 自定义提交的数据
     */
    protected function search_by_pdata($mname = '', $pdata = array(), $select = '*')
    {
        if (empty($mname)) {
            $mname = $this->model_name;
        }

        if (empty($mname)) {
            return array(1, '模型Model未定义');
        }

        //获取表单中的数据
        if (empty($pdata)) {
            $pdata = $this->prep_data(M($mname)->table_name, FALSE, TRUE);
        }

        //自定义条件

        //主键名称
        $key = M($mname)->get_pk();

        //查询条件
        $where = array();

        //逻辑删除
        if (M($mname)->field_exists('is_del')) {
            $where['is_del'] = 0;
        }

        //主键需要精确查询时
        if (isset($key) && isset($pdata[$key])) {
            $where[$key] = $pdata[$key];
            unset($pdata[$key]);
        }

        //整数/enum/set/类型,精确匹配
        $fields = $this->db->field_data(M($mname)->table_name);
        foreach ($fields as $field) {
            if (isset($pdata[$field->name]) && in_array(strtolower($field->type), array('int', 'tinyint', 'bit', 'mediumint', 'bigint', 'smallint', 'enum', 'set'))) {
                $where[$field->name] = $pdata[$field->name];
                unset($pdata[$field->name]);
            }
        }

        //in 操作
        if (isset($pdata['in'])) {
            $where['in'] = $pdata['in'];
            unset($pdata['in']);
        }

        //其它字段模糊查询
        if (!empty($pdata))
            $where['like'] = $pdata;

        //检索
        $data = self::search_grid($where, $mname, $select);

        //日志调试输出
        log_message('debug', "\nwhere:\n" . var_export($where, true));
        return array(0, $data);
    }

    /**
     * 新增/修改接口
     *
     * mname 模型名称
     * pdata 自定义提交的数据
     */
    public function save_by_pdata($mname = '', $pdata = array())
    {
        if (empty($mname)) {
            $mname = $this->model_name;
        }

        if (empty($mname)) {
            return array(1, '模型Model未定义');
        }

        //主键名称
        $key = M($mname)->get_pk();
        $key_value = $this->post($key);

        if (empty($pdata)) {
            //过滤数据，去除非数据库字段数据
            $pdata = $this->prep_data(M($mname)->table_name, FALSE, FALSE);
        }

        //自定义数据处理
        //是否有更新时间字段
        if (M($mname)->field_exists('update_at')) {
            $pdata['update_at'] = time();
        }

        //最后一个修改人
        if (intval($this->G['uid']) > 0 && M($mname)->field_exists('update_uid')) {
            $pdata['update_uid'] = $this->G['uid'];
        }

        //是否有逻辑删除字段 0 正常 1已逻辑删除
        if (M($mname)->field_exists('is_del')) {
            $pdata['is_del'] = 0;
        }

        //存在主键为更新，否则新增
        if (is_post() && !empty($this->post($key))) {
            //自定义附加条件

            $update_result = M($mname)->update(array($key => $key_value), $pdata);
            //日志调试输出
            log_message('debug', "\npdata:\n" . var_export($pdata, true));
            log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));

            //执行数据库操作
            if ($update_result) {
                return array(0, '更新数据成功');
            } else {
                $err_msg = '更新数据失败.' . cur_page_url() . " " . var_export($this->request(), true) . ' ' . $this->db->_error_message();
                log_message('error', $err_msg);

                if (ENVIRONMENT == 'development') {
                    return array(1, $err_msg);
                } else {
                    return array(1, '更新数据失败');
                }
            }
        } else if (is_post() && empty($key_value)) {
            //自定义附加条件
            if (M($mname)->field_exists('create_at') && empty($pdata['create_at'])) {
                $pdata['create_at'] = time();
            }
            //数据添加人
            if (intval($this->G['uid']) > 0 && M($mname)->field_exists('create_uid')) {
                $pdata['create_uid'] = $this->G['uid'];
            }

            //执行数据库操作
            unset($pdata[$key]);//去除主键值

            $insert_result = M($mname)->insert($pdata);
            //日志调试输出
            log_message('debug', "\npdata:\n" . var_export($pdata, true));
            log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));

            if ($insert_result) {
                return array(0, '添加数据成功');
            } else {
                $err_msg = '添加数据失败.' . cur_page_url() . " " . var_export($this->request(), true) . ' ' . $this->db->_error_message();
                log_message('error', $err_msg);

                if (ENVIRONMENT == 'development') {
                    return array(1, $err_msg);
                } else {
                    return array(1, '添加数据失败');
                }
            }
        } else {
            return array(1, '添加数据失败');
        }
    }

    /**
     * 删除接口
     * mname 模型名称
     */
    public function del_by_pdata($mname = '', $pdata = array())
    {
        if (empty($mname)) {
            $mname = $this->model_name;
        }

        if (empty($mname)) {
            return array(1, '模型Model未定义');
        }

        //主键名称
        $key = M($mname)->get_pk();

        //默认前台提交的字段名称是id
        $ids = (array)$this->post("id");

        //自定义附加条件

        //开始删除
        if (is_post() && is_array($ids)) {
            //是否需要逻辑删除
            if (M($mname)->field_exists('is_del')) {
                $pdata['is_del'] = 1;
                foreach ($ids as $id) {
                    //逻辑删除
                    M($mname)->update(array($key => $id), $pdata);
                }

                //日志调试输出
                log_message('debug', "\nids:\n" . var_export($ids, true));
                log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));
                return array(0, '删除成功');
            } else {
                //物理删除
                $del_result = M($mname)->delete_batch((array($key => $ids)));
                //日志调试输出
                log_message('debug', "\nids:\n" . var_export($ids, true));
                log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));

                if ($del_result) {
                    return array(0, '删除成功');
                } else {
                    return array(1, '删除失败');
                }
            }
        } else {
            return array(1, '删除失败');
        }
    }


    /**
     *
     * 基本 TreeGrid 数据查询
     * @param array $where
     * @param string $id_field tree 使用的 idField
     * @param string $text_field tree 使用的 textField
     * @return string
     */
    protected function search_tree($where = array(), $id_field = 'id', $text_field = 'name', $mname = '')
    {
        $result = self::_search($where, $id_field, $text_field, 'tree', $mname);
        $data = $result['rows'];
        return $data;
    }

    /**
     * 基本 Datagrid 数据查询
     * @param array $where
     * @return array|string
     */
    protected function search_grid($where = array(), $mname = '', $select = '*')
    {
        $data = self::_search($where, '', '', 'grid', $mname, $select);
        return $data;
    }

    /**
     * easyui 基本数据查询
     * @param array $where
     * @param string $id_field tree 使用的 idField
     * @param string $text_field tree 使用的 textField
     * @param string $type 组织数据的格式
     * @return array
     */
    private function _search($where = array(), $id_field = 'id', $text_field = 'name', $type = 'grid', $mname = '', $select = '*')
    {
        if (empty($mname)) {
            $mname = $this->model_name;
        }
        if (empty($mname)) {
            $this->render_ajax(1, '模型Model未定义');
        }

        $page = max($this->request('page'), 1);
        // 需要显示的行数
        $rows = $this->request('rows');
        $sort = $this->request('sort');
        $order = $this->request('order', 'desc');
        $total = 0;
        if (!empty($rows) && $rows > 0) {
            $limit = array($rows, $rows * ($page - 1));
        } else {
            $limit = '';
        }
        if (!empty($sort)) {
            $order_by = $sort . ' ' . $order;
        } else {
            $order_by = '';
        }
        $list = $this->get_model($mname)->get_all($where, $limit, $order_by, $select);
        if ($type == 'tree') {
            foreach ($list as $key => $value) {
                $list[$key]['id'] = $list[$key][$id_field];
                $list[$key]['text'] = $list[$key][$text_field];
            }
        } else {
            $total = $this->get_model($mname)->get_count($where);
        }

        log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));
        return array('total' => $total, 'rows' => $list);
    }
}