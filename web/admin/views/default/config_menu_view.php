<script>
    var save_url = '/admin/config_menu/save';
    var del_url = '/admin/config_menu/del';

    var get_menu_url = '/admin/config_menu/get_all_menu';
</script>
<style type="text/css">
    .tree-node-selected {
        border: 1px solid grey;
        font-weight: bold;
    }
</style>
<!-- 工具栏 -->
<div id="tb" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="add()">添加子分类</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="save()">保存</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="del()">删除</a>
    </div>
</div>
<div id="mainLayout" class="easyui-layout hidden" data-options="fit: true">
    <!--左侧 表格-->
    <div data-options="region:'west',border:false,split:true,iconCls: 'icon-application_view_tile'"
         style="width: 260px;padding-top:10px;padding-left:5px;">
        <ul id="admin_config_menu_tree" class="easyui-tree" data-options="fit:true,border:false">
        </ul>
    </div>
    <!--中部 表格-->
    <div data-options="region:'center',border:false" style="padding:10px;">
        <form id="fm" method="post" style="padding: 0">
            <input type="hidden" name="id" id="id">
            <div class="easyui-panel" title="菜单设置"
                 data-options="footer:'#tb',noheader:true">
                <div id="mainTab" class="easyui-tabs" data-options="border:false">
                    <div title="基本设置" style="padding:10px">
                        <table cellpadding="5">
                            <tr>
                                <td>上级分类:</td>
                                <td colspan="2"><input id="tree" class="easyui-combotree" name="parentid"
                                                       style="width: 260px;"></td>
                            </tr>
                            <tr>
                                <td>排序:</td>
                                <td colspan="2"><input class="easyui-textbox" name="listorder"></td>
                            </tr>
                            <tr>
                                <td>名称:</td>
                                <td colspan="2"><input type="text" name="name" class="easyui-textbox"
                                                       data-options="required:true"></td>
                            </tr>
                            <tr>
                                <td>菜单图标:</td>
                                <td><input class="easyui-textbox" type="text" name="icon"></td>
                                <td><a href="/static/easyui/plus/icons/" target="_blank">菜单集合</a></td>
                            </tr>
                            <tr>
                                <td>是否显示:</td>
                                <td style="text-align:left"><input type="radio" value="1" name="display"> 是 <input
                                        type="radio" value="0"
                                        name="display"> 否
                                </td>
                                <td>选择
                                    <是>，是否开启给其他用户组使用，再添加权限的时候可以选择。当选择
                                        <否>，是不能开启给其他管理组。只能创始人才能操作
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    //最后被选中的节点ID
    var select_node_id = 0;
    $(function () {
        $('#fm').form('load', {parentid: 0, display: 1, listorder: 50});


        $('#admin_config_menu_tree').tree({
            title: '',
            url: get_menu_url,
            parentField: 'parentid',
            animate: false,
            lines: true,
            loadFilter: function (data) {
                var root = [];
                var node = {id: 0, text: '根结点', state: 'open'};
                node.children = data;
                root.push(node);
                return root;
            },
            formatter: function (node) {//格式化结果，显示子菜单数量
                var s = node.text;
                s += '&nbsp;<span style=\'color:grey\'>(' + node.id + ')</span>';
                return s;
            },
            onClick: function (node) {
                edit();
                $(this).tree('expand', node.target)
                select_node_id = node.id;
            },
            onDblClick: function (node) {
                $(this).tree('collapseAll', node.target)
            },
            onExpand: function (node) {
                store_tree_state('admin_config_menu_tree');
            },
            onCollapse: function (node) {
                store_tree_state('admin_config_menu_tree');
            },
            onLoadSuccess: function (node, data) {
                //$('#admin_config_menu_tree').tree('expandAll');
                restore_tree_state('admin_config_menu_tree');
                if (select_node_id > 0) {
                    var node = $(this).tree('find', select_node_id);
                    if (node) {
                        $(this).tree('select', node.target);
                        $(this).tree('scrollTo', node.target);
                    }
                }
            }
        });


        $('#tree').combotree({
            collapsible: true,
            url: get_menu_url,
            loadFilter: function (data) {
                var root = [];
                var node = {id: 0, text: '根结点', state: 'open'};
                node.children = data;
                root.push(node);
                return root;
            },
            onSelect: function (row) {
                $('#fm').form('load', {
                    display: 1
                });
            },
            onLoadSuccess: function (node, data) {
            }

        });

    });

    function add() {
        $('#fm').form('clear');
        var row = $('#admin_config_menu_tree').tree('getSelected');
        if (row) {
            //alert(row.id);
            $('#tree').combotree('setValue', row.id);
            $('#fm').form('load', {
                display: 1,
                listorder: 50
            });
        } else {
            $('#tree').combotree('setValue', 0);
            $('#fm').form('load', {
                display: 1,
                listorder: 50
            });
        }
    }

    /**
     *  编辑分类
     */
    function edit() {
        var row = $('#admin_config_menu_tree').tree('getSelected');
        if (row) {
            $('#fm').form('load', row);
        }
        else{
            show_msg('请先选择一条记录.', 'error');
        }
    }

    /**
     * 删除分类
     */
    function del() {
        var row = $('#admin_config_menu_tree').tree('getSelected');
        var parent = $('#admin_config_menu_tree').tree('getParent', row.target);
        $.messager.confirm('确认', '是否确认删除-' + row.title, function (r) {
            if (r) {
                $.post(del_url, {'id': row.id}, function (data) {
                    if (data.retcode == 0) {
                        show_msg(data.msg);
                        if (parent) {
                            select_node_id = parent.id;
                        }
                        $('#admin_config_menu_tree').tree('reload');
                    } else {
                        show_msg(data.msg, 'error');
                    }
                    $('#fm').form('load', {
                        is_custom: 0,
                        display: 1,
                        listorder: 50
                    });
                }, 'json');
            }

        });
    }

    /**
     * 保存分类
     */
    function save() {
        if ($('#fm').form('validate')) {

            var m1 = $('#id').val();
            var m2 = $('#tree').val();
            if (m1 && m2 && m1 == m2) {
                show_msg('上级分类与当前分类不能相同.', 'error');
                return false;
            }
            //alert(parseInt(m2));
            if (m2 == "") {
                show_msg('请在左侧重新选择分类.并点击：【添加子分类】 按钮', 'error');
                return false;
            }

            msg_progress();
            var post_data = $('#fm').serialize();
            $.ajax({
                url: save_url,
                type: 'post',
                data: post_data,
                dataType: 'json',
                beforeSend: function () {
                    msg_progress();
                },
                success: function (data) {
                    if (data.retcode == 0) {
                        msg_progress_close();
                        show_msg(data.msg);
                        if (data.data.id) {
                            select_node_id = data.data.id;
                        }
                        $('#admin_config_menu_tree').tree('reload');
                    } else if (data.retcode == 2) {
                        window.location.reload();
                    } else {
                        msg_progress_close();
                        show_msg(data.msg, 'error');
                    }
                },
                error: function (data, status, e) {
                    var msg = JSON.stringify(data)
                    if (typeof(data.responseJSON) != 'undefined' && typeof(data.responseJSON.msg) != 'undefined') {
                        msg = data.responseJSON.msg;
                    }
                    show_msg(msg, '', '', 50 * 1000, '98%', '98%');
                    msg_progress_close();
                }
            });
        }
    }
</script>