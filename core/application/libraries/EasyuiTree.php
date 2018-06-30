<?php

/**
 * 通用的Easyui树型结构生成
 */
class EasyuiTree
{
    /**
     * 所有节点
     * @var array
     */
    var $nodes = array();
    var $times = 0;

    /*
     * 数据需包括：
     * id 编号
     * parentid 父节点
     * display 是否显示
     */
    function __construct($nodes = array())
    {
        $this->nodes = $nodes;
    }

    public function set_nodes($nodes = array())
    {
        $this->nodes = $nodes;
    }

    /*
     *  $id 节点编号
     *  $display 是否展示隐藏节点
     *  $display_menu_ids  是否只展示指定节点
     */
    public function get_tree_nodes($id = 0, $display_all = FALSE, $display_nodes_id = array())
    {
        $id = intval($id);

        //只显示display=1的菜单
        $result = $this->get_child($id, $display_all, $display_nodes_id);

        $tree = array();

        if (is_array($result))

            foreach ($result as $key => $value) {
                //过滤菜单
                if (is_array($display_nodes_id) && count($display_nodes_id) > 0 && !in_array($result[$key]['id'], $display_nodes_id)) {
                    continue;
                }

                if ($value['islast'] < 1) {
                    if (!isset($result[$key]['state']) || $result[$key]['state'] != 'open') {
                        $result[$key]['state'] = "closed";
                    }
                    $result[$key]['children'] = $this->get_tree_nodes($value['id'], $display_all, $display_nodes_id);
                } else {
                    $result[$key]['children'] = '';
                }

                //因为有一些不显示的子结点，所以如果所有子结点都不显示，树就展开
                if (empty($result[$key]['children']) || count($result[$key]['children']) == 0) {
                    unset($result[$key]['state']);
                }
                $tree[] = $result[$key];
            }
        return $tree;
    }

    public function get_child($id, $display_all = FALSE, $display_menu_ids = array())
    {
        $newarr = array();
        if (is_array($this->nodes)) {
            foreach ($this->nodes as $menu) {
                //是否跳过隐藏节点
                if (!$display_all && $menu['display'] == 0) {
                    continue;
                }
                //是否只显示指定节点
                if (is_array($display_menu_ids) && count($display_menu_ids) > 0 && !in_array($menu['id'], $display_menu_ids)) {
                    continue;
                }
                //设置节点信息
                if ($menu['parentid'] == $id) $newarr[] = $menu;
            }
        }
        return $newarr ? $newarr : false;
    }

}