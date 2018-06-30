<script>
    var search_url = '/admin/user/search';
    var save_url = '/admin/user/save';
    var del_url = '/admin/user/del';
    var offline_by_sesseion_id_url = '/admin/user/offline_by_sesseion_id';

    var search_user_log = '/admin/user/log';
    var search_user_session = '/admin/user/session';
</script>
<style>
    /*
    修改datagrid cell 行高

    .datagrid-header-rownumber {
        height: 35px;
    }

    .datagrid-cell-rownumber {
        height: 35px;
    }

     */
    /*修改 linkbutton 高度*/
    .l-btn-text {
        display: inline-block;
        vertical-align: top;
        width: auto;
        line-height: 18px;
        font-size: 12px;
        padding: 0;
        margin: 0 4px;
    }
</style>

<!-- 页面布局 -->
<div id="mainLayout" class="easyui-layout hidden" data-options="fit:true">
    <!--头部 搜索-->
    <div data-options="region:'north',border:false" style="height:40px;padding:5px;border-bottom: 1px solid #CCCCCC">
        <form id="search_form" name="search_form">
            uid: <input name="uid" class="easyui-textbox" style="width:80px">
            &nbsp;
            用户名: <input name="username" class="easyui-textbox" style="width:80px">
            &nbsp;
            昵称: <input name="nickname" class="easyui-textbox" style="width:80px">
            &nbsp;
            邮箱: <input name="email" class="easyui-textbox" style="width:80px">
            &nbsp;
            状态:
            <select class="easyui-combobox" name="status" data-options="editable:false" style="width:80px">
                <option value="">全部</option>
                <option value="0">正常</option>
                <option value="1">禁用</option>
            </select>
            &nbsp;
            <a href="#" class="easyui-linkbutton" iconCls="icon-search"
               onclick="do_search_grid('#grid','#search_form')">搜索</a>
        </form>
    </div>

    <!--中部 表格-->
    <div class="easyui-layout hidden" data-options="region:'center',border:false">

        <!-- 左侧 -->
        <div data-options="region:'center',border:false" title="用户列表">
            <table id="grid" data-options="fit:true,border:false">
            </table>
        </div>

        <!-- 右侧 -->
        <div data-options="region:'east',split:true,border:false" style="width:50%;">
            <div class="easyui-tabs" data-options="border:false,fit:true,">
                <div title="操作日志" data-options="iconCls:'icon-page_white_edit'">
                    <table id="grid_user_log" data-options="fit:true,border:false">
                    </table>
                </div>
                <div title="在线窗口" data-options="iconCls:'icon-lightning'">
                    <table id="grid_user_session" data-options="fit:true,border:false">
                    </table>
                </div>
            </div>
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

<!-- 工具栏 -->
<div id="tb_user_session" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-link_break" plain="true" onclick="offline_by_sesseion_id()">下线</a>
    </div>
</div>
<!-- 编辑表单 -->
<div id="dlg" class="easyui-dialog" iconCls="icon-image_edit" style="width:500px;height:380px;padding:10px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post">
        <div class="ftitle">基础信息</div>
        <div style="display: none">
            <label>隐藏字段:</label>
            <input name="uid" data-options="label:'uid:'"/>
        </div>
        <div class="fitem">
            <input id="username" name="username" data-options="label:'用户名:',required:true" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="nickname" data-options="label:'姓名:',required:true"
                   class="easyui-textbox" style="width:300px">
        </div>
        <div class="fitem">
            <input name="email" data-options="label:'邮箱:'" validType='email' class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="weixin" data-options="label:'微信:'" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="qq" data-options="label:'qq:'" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="mobile" data-options="label:'手机:'" validType='mobile' class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="password2" type="password" data-options="label:'密码:'" class="easyui-textbox"
                   style="width:300px"><span>&nbsp;<font color="red">【不修改密码，请留空。】</font></span>
        </div>
        <div class="fitem">
            <select class="easyui-combobox" id="status" name="status" data-options="required:true,editable:false" label="状态:"
                    style="width:300px">
                <option value="0">未审核</option>
                <option value="1">正常</option>
                <option value="2">不通过</option>
                <option value="3">冻结</option>
                <option value="4">停用</option>
            </select>
        </div>
    </form>
</div>
<div id="dlg-buttons">
    <a id="saveBtn" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" onclick="save()">保存</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
       onclick="javascript:$('#dlg').dialog('close')">取消</a>
</div>

<!-- JS -->
<script>
    $(function () {
        var grid = $('#grid').datagrid({
            title: '',
            url: search_url,
            striped: true,
            rownumbers: true,
            pagination: true,
            singleSelect: false,
            idField: 'uid',
            sortName: 'uid',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            toolbar: '#tb',
            resizable: true,
            frozenColumns: [
                [
                    {field: 'ck', title: 'UID', checkbox: true, width: 120}
                ]
            ],
            columns: [
                [
                    /*
                    {
                        title: '状态', field: 'user_status', sortable: true,
                        formatter: function (value, row, index) {
                            //icon-lightbulb_off
                            //icon-lightbulb
                            return '<a href="#" onclick="setTimeout(offline,10)" class="easyui-linkbutton" iconCls="icon-lightbulb_off" data-options="plain:true"></a>';
                        }
                    },
                    */
                    {title: 'UID', field: 'uid', sortable: true},
                    {title: '用户名', field: 'username', sortable: true},
                    {title: '姓名', field: 'nickname', sortable: true},
                    {title: '邮箱', field: 'email', sortable: true},
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
                    {
                        title: '注册时间', field: 'create_at', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substring(2);
                        }
                    }
                    /*,
                    {
                        title: '管理操作', field: 'admin_control', sortable: true,
                        formatter: function (value, row, index) {
                            return '<a href="#" onclick="setTimeout(offline,10)" class="easyui-linkbutton c5">下线</a>';
                        }
                    }
                    */
                ]
            ],
            onClickRow: function (rowIndex, rowData) {
                //行点击事件
                $('#grid').datagrid("clearSelections");
                $('#grid').datagrid("selectRow", rowIndex);
                //同步显示组成员
                $('#grid_user_log').datagrid({
                    url: search_user_log,
                    queryParams: {
                        uid: rowData.uid
                    }
                });
                //同步显示组成员
                $('#grid_user_session').datagrid({
                    url: search_user_session,
                    queryParams: {
                        uid: rowData.uid
                    }
                });
            },
            onClickCell: function (rowIndex, rowData) {
                //行点击事件
            },
            onDblClickRow: function (rowIndex, rowData) {
                //行双击事件
                edit();
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
                $(this).datagrid('getPanel').find('a.easyui-linkbutton').linkbutton();
            }
        });


        var grid_user_log = $('#grid_user_log').datagrid({
            title: '',
            striped: true,
            rownumbers: true,
            pagination: true,
            singleSelect: true,
            sortName: 'time',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            resizable: true,
            columns: [
                [
                    {title: '信息', field: 'msg', width: 150, sortable: true},
                    {
                        title: '时间', field: 'time', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substring(2);
                        }
                    },
                    {title: 'ip', field: 'ip', sortable: true},
                    {title: 'ip来路', field: 'ip_msg', sortable: true}
                ]
            ],
            onLoadSuccess: function (data) {
                //加载或翻页后默认选中第一行
                $(this).datagrid('scrollTo', 0);
            }
        });

        var grid_user_session = $('#grid_user_session').datagrid({
            title: '',
            toolbar: '#tb_user_session',
            striped: true,
            rownumbers: true,
            pagination: true,
            singleSelect: true,
            sortName: 'time',
            sortOrder: 'desc',
            pageSize: 500,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            resizable: true,
            columns: [
                [
                    {title: '渠道', field: 'site', sortable: true},
                    {
                        title: '时间', field: 't', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substring(2);
                        }
                    },
                    {title: 'ip', field: 'ip', sortable: true},
                    {title: 'ua', field: 'ua', sortable: true},
                    {title: '停留', field: 'cur_page', sortable: true}
                ]
            ],
            onLoadSuccess: function (data) {
                //加载或翻页后默认选中第一行
                $(this).datagrid('scrollTo', 0);
            }
        });

    });

    function add() {
        $('#fm').form('clear');
        $('#dlg').dialog('open').dialog('setTitle', '新增');
        $('#username').textbox('enable');
        $('#status').combobox('setValue', "1");
        $('#username').textbox('textbox').focus();
    }

    function edit() {
        $('#fm').form('clear');
        var row = $('#grid').datagrid('getSelected');
        if (row) {
            $('#dlg').dialog('open').dialog('setTitle', '修改');
            $('#fm').form('load', row);
            $('#username').textbox('disable');
        }
        else {
            show_msg('请先选择一条记录.', 'error');
        }
    }

    function del() {
        var ids = [];
        var strs = [];
        var rows = $('#grid').datagrid('getSelections');
        for (var i = 0; i < rows.length; i++) {
            ids.push(rows[i].uid);
            strs.push("<b><font color='red'>" + rows[i].username + "</font></b>(" + rows[i].uid + ")");
        }
        if (rows) {
            $.messager.confirm('确认', '是否确认删除以下用户:' + strs, function (r) {
                if (r) {
                    $.post(del_url, {id: ids}, function (data) {
                        if (data.retcode == 0) {
                            show_msg(data.msg);
                            $('#grid').datagrid('reload'); // reload the user data
                            $('#grid').datagrid('clearChecked'); // 清除选中行

                        } else {
                            show_msg(data.msg, 'error');
                        }
                    }, 'json');
                }
            });
        }
    }

    function offline_by_sesseion_id() {
        var row_user = $('#grid').datagrid('getSelected');
        var uid = 0;
        if (row_user) {
            uid = row_user['uid'];
        }

        var ids = [];
        var strs = [];
        var rows = $('#grid_user_session').datagrid('getSelections');
        for (var i = 0; i < rows.length; i++) {
            ids.push(rows[i].sid);//session id
            strs.push("<b><font color='red'>" + rows[i].site + "</font></b>(" + rows[i].ip + ")");
        }
        if (rows) {
            $.messager.confirm('确认', '是否确认下线以下窗口:' + strs, function (r) {
                if (r) {
                    $.post(offline_by_sesseion_id_url, {id: ids, uid: uid}, function (data) {
                        if (data.retcode == 0) {
                            show_msg(data.msg);
                            $('#grid').datagrid('reload'); // reload the user data
                            $('#grid').datagrid('clearChecked'); // 清除选中行
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
            var post_data = $('#fm').serialize();

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
                        $('#grid').datagrid('reload'); // reload the user data
                        $('#grid').datagrid('clearChecked'); // 清除选中行
                    } else {
                        show_msg(data.msg, 'error');
                    }
                }
            });

        }
    }
</script>