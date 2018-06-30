<script>
    var search_url = '/admin/role/search';
    var save_url = '/admin/role/save';
    var del_url = '/admin/role/del';
    var save_priv_url = '/admin/role/save_priv';
    var get_priv_menu_url = '/admin/role/get_priv_menu';

    var user_url = '/admin/role/list_user';
    var save_user_url = '/admin/role/save_user';
    var del_user_url = '/admin/role/del_user';

    var menu_url = '/admin/menu/get_all_menu';
    var get_role_url = '/admin/role/get_all_role';
</script>
<style type="text/css">
    .tree-node-selected {
        border: 1px solid #000;
        font-weight: bold;
    }
</style>
<!-- 页面布局 -->
<div id="mainLayout" class="easyui-layout hidden" data-options="fit: true">

    <!--左侧 表格-->
    <div data-options="region:'west',split:true,border:false" style="width:300px;" title="角色列表">
        <table id="grid" data-options="fit:true,border:false">
        </table>
    </div>


    <!--中间 表格-->
    <div class="easyui-layout hidden" data-options="region:'center',border:false,fit: true">

        <div data-options="region:'center',split:true,border:false" title="用户列表">
            <table id="grid_user" data-options="fit:true,border:false">
            </table>
        </div>

        <!-- 右侧 表格 -->
        <div data-options="region:'west',split:true,border:false,footer:'#tb_priv'" title="权限设置" style="width:280px;">
            <ul id="tt" class="easyui-tree" data-options="fit:true,border:false"
                style="padding-top: 5px;padding-left: 5px;">
            </ul>
        </div>

    </div>
</div>


<!-- 工具栏 -->
<div id="tb" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="add()">添加</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="edit()">修改</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="del()">删除</a>
    </div>
</div>
<div id="tb_user" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-user_add" plain="true" onclick="add_user()">添加成员</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-user_delete" plain="true" onclick="del_user()">删除成员</a>
    </div>
</div>
<div id="tb_priv" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px;text-align: right">
        <a href="#" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="save_priv()">保存权限</a>
    </div>
</div>

<!-- 编辑表单 -->
<div id="dlg" class="easyui-dialog" iconCls="icon-image_edit" style="width:420px;height:280px;padding:10px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" style="padding-top: 20px;">
        <div style="display: none">
            <label>隐藏字段:</label>
            <input name="gid" data-options="label:'gid:'"/>
        </div>
        <div class="fitem">
            <input name="gname" data-options="label:'角色名称:',required:true" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <select class="easyui-combobox" name="is_backend_admin" data-options="required:true,editable:false"
                    label="是否可登录后台:"
                    style="width:300px">
                <option value="0">否</option>
                <option value="1">是</option>
            </select>
        </div>
        <div class="fitem">
            <input id="tree" class="easyui-combotree" name="parentid" data-options="label:'父节点:',required:true"
                   style="width: 300px;">
        </div>
    </form>
</div>
<div id="dlg-buttons">
    <a id="saveBtn" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" onclick="save()">保存</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
       onclick="javascript:$('#dlg').dialog('close')">取消</a>
</div>

<!-- 编辑表单 -->
<div id="dlg_user" class="easyui-dialog" iconCls="icon-image_edit" style="width:420px;height:200px;padding:10px"
     closed="true" buttons="#dlg-buttons_user">
    <form id="fm_user" method="post" style="padding-top: 20px;">
        <div style="display: none">
            <label>隐藏字段:</label>
            <input name="gid" data-options="label:'gid:'"/>
        </div>
        <div class="fitem">
            <input name="username" data-options="label:'用户名:',required:true" class="easyui-textbox" style="width:300px">
        </div>
        <!--
        <div class="fitem">
            <input name="email" data-options="label:'邮箱:',required:true" class="easyui-textbox" style="width:300px">
        </div>
        -->
    </form>
</div>
<div id="dlg-buttons_user">
    <a id="saveBtn" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" onclick="save_user()">保存</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
       onclick="javascript:$('#dlg_user').dialog('close')">取消</a>
</div>
<!-- JS -->
<script>

    function save_priv() {
        var ids = [];
        var nodes = $('#tt').tree('getChecked', ['checked', 'indeterminate']);
        for (var i = 0; i < nodes.length; i++) {
            ids.push(nodes[i].id);
        }
        var cp = $('#grid').treegrid('getSelected');
        if (cp) {
            var gid = cp.gid;
            if (ids && ids.length > 0) {
                $.messager.confirm('确认', '是否更新角色[' + cp.gname + ']的权限?权限ID:' + ids, function (r) {
                    if (r) {
                        $.post(save_priv_url, {
                                id: ids, gid: gid
                            },
                            function (data) {
                                if (data.retcode == 0) {
                                    show_msg(data.msg);
                                } else {
                                    show_msg(data.msg, 'error');
                                }
                            }, 'json'
                        );
                    }
                });
            }
        }
        else {
            show_msg('请先选择角色组', 'error')
        }
    }

    $(function () {
        $('#tt').tree({
            title: '',
            url: menu_url,
            parentField: 'parentid',
            animate: true,
            lines: true,
            checkbox: true,
            loadFilter: function (data) {
                var root = [];
                var node = {id: 0, text: '根结点', state: 'open'};
                node.children = data;
                root.push(node);
                return root;
            },
            formatter: function (node) {
                var s = node.text;
                s += '&nbsp;<span style=\'color:blue\'>(' + node.id + ')</span>';
                return s;
            },
            onClick: function (node) {
                $('#tt').tree('expand', node.target)
            },
            onLoadSuccess: function (node, data) {
                $('#tt').tree('expandAll')
            }
        });
        var grid = $('#grid').treegrid({
            url: search_url,
            striped: true,
            rownumbers: true,
            idField: 'gid',
            treeField: 'gname',
            toolbar: '#tb',
            //resizable: true,
            columns: [
                [
                    {title: 'ID', field: 'gid', sortable: true},
                    {title: '角色名称', field: 'gname', sortable: true},
                    {
                        title: '后台登录', field: 'is_backend_admin', sortable: true,
                        formatter: function (value, row, index) {
                            if (value == 0) {
                                return '<span><font color="red">否</font></span>';
                            } else if (value == 1) {
                                return '<span><font color="green">是</font></span>';
                            }
                        }
                    }
                ]
            ],
            onClickRow: function (row) {
                //行点击事件
                $(this).treegrid("unselectAll");
                $(this).treegrid("select", row.gid);

                //列出用户
                list_user();

            },
            onSelect: function (row) {
                //行选择事件
                if (row) {
                }
            },
            onLoadSuccess: function (data) {
                //加载或翻页后默认选中第一行
                $(this).datagrid('scrollTo', 0);
                $(this).datagrid('selectRow', 0);
                //同步显示组成员
                $('#grid_user').datagrid({
                    queryParams: {
                        gid: 1
                    }
                });
            }
        });


        //用户列表
        var grid_user = $('#grid_user').datagrid({
            title: '',
            url: user_url,
            striped: true,
            rownumbers: false,
            pagination: true,
            singleSelect: false,
            idField: 'uid',
            sortName: 'uid',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            toolbar: '#tb_user',
            resizable: true,
            frozenColumns: [
                [
                    {field: 'ck', title: 'UID', checkbox: true, width: 120}
                ]
            ],
            columns: [
                [
                    {title: 'UID', field: 'uid', sortable: true},
                    {title: '用户名', field: 'username', sortable: true},
                    {title: '姓名', field: 'nickname', sortable: true},
                    {
                        title: '状态', field: 'status', sortable: true,
                        formatter: function (value, row, index) {
                            if (value == 0) {
                                return '<span><font color="red">未审核</font></span>';
                            } else if (value == 1) {
                                return '<span><font color="green">正常</font></span>';
                            } else if (value == 2) {
                                return '<span><font color="red">不通过</font></span>';
                            } else if (value == 3) {
                                return '<span><font color="red">冻结</font></span>';
                            } else if (value == 4) {
                                return '<span><font color="red">停用</font></span>';
                            }
                            else {
                                return '<span><font color="red">未知</font></span>';
                            }
                        }
                    },
                    {title: '邮箱', field: 'email', sortable: true},
                    {
                        title: '创建时间', field: 'create_at', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substring(2);
                        }
                    }
                ]
            ],
            onClickRow: function (rowIndex, rowData) {
                //行点击事件
            },
            onSelect: function (rowIndex, rowData) {
                //行选择事件
                if (rowData) {
                    //
                }
            },
            onLoadSuccess: function (data) {
                //加载或翻页后默认选中第一行
                $(this).datagrid('scrollTo', 0);
                set_priv();
            }
        });

        $('#tree').combotree({
            collapsible: true,
            url: get_role_url,
            // loadFilter: function (data) {
            //     var root = [];
            //     var node = {id: 0, text: '顶级目录', state: 'open'};
            //     node.children = data;
            //     root.push(node);
            //     return root;
            // },
            onSelect: function (row) {
                $('#fm').form('load', {
                    s: row.s,
                    c: row.c,
                    is_custom: 0,
                    display: 1
                });
            },
            onLoadSuccess: function (node, data) {
            }

        });
    });

    //列出用户
    function list_user() {
        //清空已选中的用户
        $('#grid_user').datagrid("clearSelections");
        var cp = $('#grid').treegrid('getSelected');

        //同步显示组成员
        $('#grid_user').datagrid({
            queryParams: {
                gid: cp.gid
            }
        });
    }

    //设置权限菜单
    function set_priv() {
        var row = $('#grid').treegrid('getSelected');
        if (row) {
            $.post(get_priv_menu_url, {gid: row.gid}, function (data) {
                var nodes = $('#tt').tree('getChecked');
                for (i = 0; i < nodes.length; i++) {
                    $('#tt').tree('uncheck', nodes[i].target);
                }

                if (data.retcode == 0) {
                    var priv_menu = eval(data.msg);
                    for (i = 0; i < priv_menu.length; i++) {
                        var node = $('#tt').tree('find', priv_menu[i]);
                        if (node && !node.children) {
                            //alert(node.text);
                            $('#tt').tree('check', node.target);
                        }
                    }
                } else {
                    show_msg(data.msg, 'error');
                }
            }, 'json');
        }
    }

    function add_user() {
        var row = $('#grid').treegrid('getSelected');
        if (row) {
            $('#fm_user').form('clear');
            //赋值gid
            $('#fm_user').form('load', row);
            $('#dlg_user').dialog('open').dialog('setTitle', '添加角色成员');
        }
        else {
            show_msg('未选择角色', 'error');
        }
    }

    /*
    保存用户
     */
    function save_user() {
        if ($('#fm_user').form('validate')) {
            msg_progress();
            post_data = $('#fm_user').serialize();

            $.ajax({
                url: save_user_url,
                type: 'POST',
                data: post_data,
                dataType: 'json',
                error: function (data, status, e) {
                    msg = JSON.stringify(data)
                    if (typeof(data.responseJSON) != 'undefined' && typeof(data.responseJSON.msg) != 'undefined') {
                        msg = data.responseJSON.msg;
                    }
                    show_msg(msg, '', '', 50 * 1000, '98%', '98%')
                    msg_progress_close();
                },
                success: function (data) {
                    msg_progress_close();
                    if (data.retcode == 0) {
                        show_msg(data.msg);
                        $('#dlg_user').dialog('close'); // close the dialog
                        //列出用户
                        list_user();
                    } else {
                        show_msg(data.msg, 'error');
                    }
                }
            });

        }
    }


    function del_user() {
        var ids = [];
        var ids_str = [];
        var rows = $('#grid_user').datagrid('getSelections');
        for (var i = 0; i < rows.length; i++) {
            ids.push(rows[i].uid);
            ids_str.push('<b>' + rows[i].username + '</b>(id:' + rows[i].uid + ')')
        }
        if (rows) {
            $.messager.confirm('确认', '是否确认删除以下用户的角色信息:' + ids_str, function (r) {
                if (r) {
                    $.post(del_user_url, {id: ids}, function (data) {
                        if (data.retcode == 0) {
                            show_msg(data.msg);
                            //列出用户
                            list_user();
                        } else {
                            show_msg(data.msg, 'error');
                        }
                    }, 'json');
                }
            });
        }
    }

    function add() {
        $('#fm').form('clear');
        $('#dlg').dialog('open').dialog('setTitle', '新增');
    }

    function edit() {
        $('#fm').form('clear');
        var row = $('#grid').treegrid('getSelected');
        if (row) {
            $('#dlg').dialog('open').dialog('setTitle', '修改');
            $('#fm').form('load', row);
        }
        else {
            show_msg('请先选择一条记录.', 'error');
        }
    }

    function del() {
        var ids = [];
        var ids_str = [];
        var rows = $('#grid').treegrid('getSelections');
        for (var i = 0; i < rows.length; i++) {
            ids.push(rows[i].gid);
            ids_str.push('<b>' + rows[i].gname + '</b>(id:' + rows[i].gid + ')')
        }
        if (rows) {
            $.messager.confirm('确认', '是否确认删除以下角色:' + ids_str, function (r) {
                if (r) {
                    $.post(del_url, {id: ids}, function (data) {
                        if (data.retcode == 0) {
                            show_msg(data.msg);
                            $('#grid').treegrid('reload'); // reload the user data
                            $('#grid').treegrid('clearChecked'); // 清除选中行

                        } else {
                            show_msg(data.msg, 'error');
                        }
                    }, 'json');
                }
            });
        }
    }

    function save() {
        if ($('#fm').form('validate')) {
            msg_progress();
            post_data = $('#fm').serialize();

            $.ajax({
                url: save_url,
                type: 'POST',
                data: post_data,
                dataType: 'json',
                error: function (data, status, e) {
                    msg = JSON.stringify(data)
                    if (typeof(data.responseJSON) != 'undefined' && typeof(data.responseJSON.msg) != 'undefined') {
                        msg = data.responseJSON.msg;
                    }
                    show_msg(msg, '', '', 50 * 1000, '98%', '98%')
                    msg_progress_close();
                },
                success: function (data) {
                    msg_progress_close();
                    if (data.retcode == 0) {
                        show_msg(data.msg);
                        $('#dlg').dialog('close'); // close the dialog
                        $('#grid').treegrid('reload'); // reload the user data
                        $('#grid').treegrid('clearChecked'); // 清除选中行
                    } else {
                        show_msg(data.msg, 'error');
                    }
                }
            });

        }
    }
</script>