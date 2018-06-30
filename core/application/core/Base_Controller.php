<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2017/10/4
 * Time: 23:06
 */
class Base_Controller  extends MX_Controller {
    //全局变量
    public $G = array();

    //控制器第一层
    public $_s = '';
    //控制器中间层
    public $_c = '';
    //控制器方法名
    public $_m = '';

    //模型名称
    public $model_name = "";

    //模板风格
    public $view_style = "";

    /**
     * 初始化用户信息，填充G变量
     *
     */
    public function __construct()
    {
        parent::__construct();

        // 初始化uri
        $this->_s = $this->uri->segment(1);
        $class_path = $this->router->directory . $this->router->fetch_class();
        $class_path = substr($class_path, strpos($class_path, "/") + 1);
        $this->_c = $class_path;
        $this->_m = $this->router->fetch_method();

        //默认使用模块的default模板
        $this->view_style = $this->router->module . "/default";

        //设置在线信息
        update_user_session();

        //同个ip限定最多客户端(session数量)

        //植入来路或推荐人
        if (str_exists(cur_page_url(), 'uid') || isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_top_domain()) === false) {
            set_user_refer();
        }

    }

    /**
     *
     * 扩展ci处理视图功能，提供类似嵌入式布局
     *
     */

    protected function show($template, $data = array())
    {
        $path = $this->view_style . "/";
        $this->load->view($path . 'common/header', $data);
        $this->load->view($path . $template, $data);
        $this->load->view($path . 'common/footer', $data);
    }

    /**
     * 规范ajax请求
     *
     * @param $retcode num 状态码
     * @param string   自定义消息
     * @param mixed    待返回的数据
     ************************
     * *状态码对应表:
     ************************
     * 0 成功请求
     * 1 自定义错误
     * 2 未登录/用户
     * 3 权限受限
     * 4 服务器内部错误/网络忙
     *******************************************
     */

    protected function render_ajax($retcode = 1, $msg = '', $data = array())
    {
        $code2msg = array(
            0 => '成功',
            // 自定义错误
            1 => '失败',
            2 => '未登录用户',
            3 => '权限受限',
            4 => '网络忙，请稍后重试'
        );

        echo json2code(
            array(
                'retcode' => $retcode,
                'msg' => $msg != '' ? $msg : (in_array($retcode, $code2msg) ? $code2msg[$retcode] : $msg),
                'data' => $data
            )
        );
        exit;
    }

    public function render_jsonp($callback, $retcode = 1, $msg = '', $data = array())
    {
        $code2msg = array(
            0 => '成功',
            // 自定义错误
            1 => '失败',
            2 => '未登录用户',
            3 => '权限受限',
            4 => '网络忙，请稍后重试'
        );
        $data = json2code(array(
            'retcode' => $retcode,
            'msg' => $msg != '' ? $msg : (in_array($retcode, $code2msg) ? $code2msg[$retcode] : $msg),
            'data' => $data
        ));
        echo "$callback('$data');";
        exit;
    }

    /**
     * 延迟按需加载模型
     *
     * @param $model_name  string 模型全名
     * @param $model_alias  string 模型别名，默认空
     * @return mixed
     */
    public function get_model($model_name, $model_alias = NULL)
    {
        static $model_container = array();
        $model_alias || $model_alias = '__' . str_replace('/', '_', $model_name);
        if (!isset($model_container[$model_alias])) {
            $this->load->model($model_name, $model_alias);
            $model_container[$model_alias] = $this->$model_alias;
        }
        return $model_container[$model_alias];
    }

    //简化post数值获取，所有数据都需要xss过滤
    public function post($name = NULL, $default = NULL)
    {
        if ($name != NULL) {
            $post = $this->input->post($name, TRUE);
            $post = $post !== FALSE ? $post : $default;
        } else {
            $post = $this->input->post(NULL, TRUE);
            $post = $post !== FALSE ? $post : array();
        }
        return $post;
    }

    //同上,获取get数值
    public function get($name = NULL, $default = '')
    {
        if ($name != NULL) {
            $get = $this->input->get($name, TRUE);
            $get = $get !== FALSE ? $get : $default;
        } else {
            $get = $this->input->get(NULL, TRUE);
            $get = $get !== FALSE ? $get : array();
        }
        return $get;
    }

    public function request($name = NULL, $default = '')
    {
        if ($name != NULL) {
            $request = $this->input->get_post($name, TRUE);
            $request = $request !== FALSE ? $request : $default;
        } else {
            $request_g = $this->get();
            $request_p = $this->post();
            $request = array_merge($request_g, $request_p);
        }
        return $request;
    }

    //调用视图
    protected function display($template, $data = array(), $theme = 'default')
    {
        $template = (array)$template;
        foreach ($template as $v) {
            $theme = $this->_s . "/" . $theme;
            $this->load->view($theme . '/' . $v, $data);
        }
    }

    /*
     * 包装POST数据，提交数据库前预处理
     * $model_name 模型名称
     * $skip_key是否忽略主键字段
     * $skip_empty是否忽略空值字段
     */
    public function prep_data($table_name, $skip_key = TRUE, $skip_empty = FALSE)
    {
        $data = [];
        $this->load->database();
        $fields = $this->db->field_data($table_name);
        foreach ($fields as $field) {
            /*
                echo $field->name;
                echo $field->type;
                echo $field->max_length;
                echo $field->primary_key;
            */

            //跳过主键
            if ($skip_key && $field->primary_key == 1) {
                continue;
            }
            //跳过空值
            $value = $this->post($field->name);
            if (is_null($value) || ($skip_empty && $value !== '0' && empty($value))) {
                continue;
            }

            $data[$field->name] = $value;
        }

        return $data;
    }
}