<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * | -------------------------------------------------------------------
 * | 后台控制器
 * | -------------------------------------------------------------------
 */
class Backend_Controller extends Backend_Db_Controller
{
    public $is_founder;
    public $menus;

    public function __construct()
    {
        parent::__construct();

        // 登录检查
        self::check_login();

        // 创始人判断
       $this->is_founder = self::check_founder($this->G['uid']);

       // 初始化权限
       self::init_priv();

        // 权限检测
       self::check_priv();

        // 初始化菜单
        $this->menus = get_sess("admin_menu");
//
//        //缓存权限菜单
        if (empty($this->menus)) {
            $this->menus = self::admin_menu();
            set_sess("admin_menu", $this->menus);
            log_message('debug', 'set admin menus success');
        }

        //日志记录
        self::oper_log();
    }


    /*
     * 公共的查询接口
     * mname 模型名称
     * pdata 自定义提交的数据
     */
    public function search()
    {
        $result = parent::search_by_pdata();
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

    /**
     * 前端新增/修改接口
     *
     * mname 模型名称
     * pdata 自定义提交的数据
     */
    public function save()
    {
        $result = parent::save_by_pdata();
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            $this->render_ajax($result[0], $result[1]);
        }
        exit;
    }

    /**
     * 前端删除接口
     * mname 模型名称
     */
    public function del()
    {
        $result = parent::del_by_pdata();
        //正确结果应为数组
        if (is_array($result) && count($result) == 2) {
            $this->render_ajax($result[0], $result[1]);
        }
        exit;
    }

    /**
     * 检查是否登录后台
     */
    final private function check_login()
    {
        if ((get_sess('is_admin') == true && $this->G['uid'])) {
            //实时判断用户是否被删除或被禁用
            $one = $this->get_user_model()->get_one(array('uid'=>$this->G['uid']),'','uid,status');
            if(!$one||$one['status']<>1)
            {
                user_offline($this->G['uid']);
                if ($this->input->is_ajax_request()) {
                    // 针对Ajax特殊反馈提示
                    $this->render_ajax(2, '用户不存在，请重新登录');
                } else {
                    show_error('用户不存在，请重新登录',403,'错误提示');
                    exit;
                }
            }
            return true;
        } else {
            if ($this->input->is_ajax_request()) {
                // 针对Ajax特殊反馈提示
                $this->render_ajax(2, '登录状态已经过期，请重新登录');
            } else {

                 //记录失效前页面地址/兼容iframe处理
                echo '<script>window.parent.location.href="' . base_url('admin/login/logout?referer=')
                    . '"+encodeURIComponent(window.parent.location.href);</script>';
                exit;
            }
        }
    }

    /**
     * 判断是否是创始人
     */
    protected function check_founder($uid = 0)
    {
        if ($uid == 0 || !preg_match('/^[1-9]{1}\d*$/', $uid)) {
            return false;
        }

        $founders = array_filter(explode(',', str_replace(' ', '', C('admin_founder'))));

        if (in_array($uid, $founders)) {
            return true;
        }

        return false;
    }

    /**
     * 初始化用户后台权限组，
     * $this->G['customperm'] = 2 创始人
     * $this->G['customperm'] =1 权限组的管理员 可以操作所属权限其他人的内容
     * $this->G['customperm'] = 0 普通权限，只能操作自己的内容
     */
    final function init_priv()
    {
        // 管理组
        $this->G['gid'] = get_sess('gid');
        if ($this->is_founder) {
            $this->G['customperm'] = 2;
        } else {
            $admin_member = $this->get_admin_member_model()->get_one(array('uid' => $this->G['uid']));
            if (!empty($admin_member) && $admin_member['customperm']) {
                $this->G['customperm'] = 1;
            } else {
                $this->G['customperm'] = 0;
            }
        }
    }

    /**
     * 后台权限判断
     * 后台限制使用 admin/admin/index 此种格式访问
     * 控制器是admin 和 login的时候不需要验证
     * 当方法是public_开头的方法无需验证
     * 当方法是ajax_开头的与普通访问的验证方式相同
     */
    final private function check_priv()
    {
        //处理HMVC问题造成的相对路径问题
        $real_c = explode("/controllers/",$this->_c);
        $real_c=$real_c[count($real_c)-1];

        // 如果访问后台首页，或者创始人访问所有页面则跳过验证
        if ($this->_c == 'admin' || $this->is_founder) {
            return true;
        }
        // 检查是否是is_custom自定义权限
        // 自定义权限跳过检查，需要自己在方法里处理权限
        $res = $this->get_admin_menu_model()->get_one(
            array(
                's' => $this->_s,
                'c' => $real_c,
                'm' => $this->_m,
                'is_custom' => 1
            )
        );

        if ($res) {
            return true;
        }

        $action = $this->_m;
        // 公共控制器，允许所有人访问
        if (preg_match('/^public_/', $this->_m)) {
            return true;
        }

        // ajax特殊处理
        if (preg_match('/^ajax_(.*)/', $this->_m, $_match)) {
            $action = $_match[1];
        }

        $res = $this->get_admin_menu_model()->get_one(
            array(
                's' => $this->_s,
                'c' => $real_c,
                'm' => $this->_m,
            )
        );

        if (!$res) {
            if ($this->input->is_ajax_request()) {
                $this->render_ajax(1, '您没有权限操作该项!');
            } else {
                show_error('您没有权限操作该项!',403,'错误提示');
            }
        }

        $ret = $this->get_admin_role_priv_model()->get_one(
            array(
                'menuid' => $res['id'],
                'gid' => $this->G['gid']
            )
        );

        // 验证通过允许访问
        if (!$ret) {
            if ($this->input->is_ajax_request()) {
                $this->render_ajax(1, '您没有权限操作该项！');
            } else {
                show_error('您没有权限操作该项',403,'错误提示');
            }
        }
    }

    /**
     * 获取对某一资源的访问权限
     * 后台限制使用 admin/admin/index 此种格式访问
     * 控制器是admin 和 login的时候不需要验证
     *
     */
    public function is_allowed($resource = array(), $uid = 0)
    {
        if (empty($resource)) {
            $resource['m'] = $this->_m;
            $resource['c'] = $this->_c;
        }

        // 不再后台地址不允许访问
        if (!isset($resource['m']) || !isset($resource['c'])) {
            return false;
        }

        // 公共首页允许访问
        if ($resource['c'] == 'admin') {
            return true;
        }

        // 公共ajax或者公共ajax方法允许访问
        if (preg_match('/^public_/', $resource['m'])) {
            return true;
        }

        if (preg_match('/^ajax_(.*)/', $resource['m'], $_match)) {
            $resource['m'] = $_match[1];
        }
        // uid不存在自动填充当前登录uid
        $uid = $uid == 0 ? ($this->G['uid'] != '' ? $this->G['uid'] : 0) : $uid;

        // 获取后台管理组
        $role = $this->get_admin_member_model()->get_one(array('uid' => $uid));

        // 创始人拥有一切特权
        if (self::check_founder($uid)) {
            return true;
        }

        // 判断权限
        $result = $this->get_admin_role_priv_model()->get_one(
            array(
                'c' => $resource['c'],
                'm' => $resource['m'],
                'gid' => $role['gid']
            )
        );

        if ($result) {
            return true;
        }

        return false;
    }


    /**
     *
     * 记录日志
     */
    final public function oper_log()
    {
        $s = $this->_s;
        $c = $this->_c;
        $m = $this->_m;
        //判断是否记录
        if (C('admin_log') == 1) {
            $time = time();
            $data['POST'] = $this->input->post();
            $data['GET'] = $this->input->server('QUERY_STRING');

            $this->get_admin_log_model()->insert(
                array(
                    'username' => $this->G['username'],
                    's' => $s,
                    'c' => $c,
                    'm' => $m,
                    'data' => json_encode($data),
                    'ip' => get_ip(),
                    'time' => time(),
                )
            );
        }
    }

    /**
     * 后台权限菜单 获取
     * @return array
     */
    final public function admin_menu()
    {
        $menu = array();

        //超级管理员返回所有菜单
        if ($this->is_founder) {
            $menu = $this->get_admin_menu_model()->get_menus();
        } else {

            $result = $this->get_admin_role_priv_model()->get_all(array('gid' => $this->G['gid']), '', '', 'menuid');
            log_message('debug', "\nsql:\n" . var_export(compile_queries(), true));
            if ($result) {
                $ids = array();
                foreach ($result as $key => $value) {
                    $ids[] = $result[$key]['menuid'];
                }
                $menu = $this->get_admin_menu_model()->get_menus(0, false, $ids);
            }
        }
       // echo json_encode($menu);exit;
        return $menu;
    }

    /**
     * //:重写render方法，将头部尾部放置在admin模块下
     * 扩展ci处理视图功能，提供类似嵌入式布局，SEO优化
     * $data array 要传递到视图的数据
     * $template string 内容模板
     * $seokey string 使用的seo数据在配置文件里的键名索引，默认不使用
     * _title, _keywords, _description为seo保留关键字,可以直接在控制器里覆盖
     * $rtemplate array替换默认模板，可选索键值，header,footer,bar,side
     *
     */
    protected function render($template, $data = array(), $seokey = 'index', $rtemplate = array())
    {
        $layouts = array(
            'header' => 'admin/common/header',
            'bar' => NULL,
            'content' => preg_match('/^admin\/(.*)/', $template) ? $template : 'admin/' . $template,
            'side' => NULL,
            'footer' => 'admin/common/footer',
        );
        // 装载SEO配置
        $this->config->load('seo', true, true);
        $seo_data = $this->config->item($seokey, 'seo');

        // 当前用户信息
        $user_info = $this->G['uid'] && $this->G['user_info'] ? $this->G['user_info'] : array();
        // 合并视图变量
        $data = array_merge(
            $seo_data ? $seo_data : array('_title' => '', '_keywords' => '', '_description' => ''),
            $data,
            array('user_info' => $user_info)
        );

        // 加载视图
        foreach ($layouts as $key => $layout) {
            if (in_array($key, array_keys($rtemplate))) {
                $layout = $rtemplate[$key];
            }
            if ($layout != NULL) {
                $this->load->view($layout, $data);
            }
        }
    }

    //
    protected function show($template, $data = array(), $use_admin_header = true)
    {
        if ($use_admin_header) {
            $path = $this->view_style . "/";
            $this->load->view('admin/default/common/header', $data);
            $this->load->view($path . $template, $data);
            $this->load->view('admin/default/common/footer', $data);
        } else {
            $path = $this->view_style . "/";
            $this->load->view($path . 'common/header', $data);
            $this->load->view($path . $template, $data);
            $this->load->view($path . 'common/footer', $data);
        }
    }

    /**
     * 实例化admin_member_model 后台管理管理组管理组成员 model
     * @return mixed
     */
    protected function get_admin_member_model()
    {
        return M('admin/sys_group_user_model');
    }

    /**
     * 实例化admin_role_priv_model 后台用户管理组权限 model
     * @return mixed
     */
    protected function get_admin_role_priv_model()
    {
        return M('admin/sys_group_priv_model');
    }

    /**
     * 实例化admin_role_model 后台用户管理组model
     * @return mixed
     */
    protected function get_admin_role_model()
    {
        return M('admin/sys_group_model');
    }

    /**
     * 实例化Admin_memu_model 后台菜单model
     * @return mixed
     */
    protected function get_admin_menu_model()
    {
        return M('admin/sys_menu_model');
    }

    /**
     * 实例化User_model 用户model
     * @return mixed
     */
    protected function get_user_model()
    {
        return M('admin/sys_user_model');
    }

    /**
     * 实例化User_model 用户model
     * @return mixed
     */
    private function get_admin_log_model()
    {
        return M('admin/sys_log_model');
    }
}