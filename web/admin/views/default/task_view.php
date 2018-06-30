<script>
    var search_url = '/admin/task/search';
    var save_url = '/admin/task/save';
    var del_url = '/admin/task/del';
    var excute_url = '/admin/task/excute';

    var search_time_url = '/admin/task/time';
    var clean_time_url = '/admin/task/clean_time';
</script>

<!-- 页面布局 -->
<div id="mainLayout" class="easyui-layout hidden" data-options="fit: true">
    <!--头部 搜索-->
    <div data-options="region:'north',border:false" style="height:40px;padding:5px;border-bottom: 1px solid #CCCCCC">
        <form id="search_form" name="search_form">
            任务名称: <input name="name" class="easyui-textbox" style="width:80px">
            &nbsp;
            <a href="#" class="easyui-linkbutton" iconCls="icon-search"
               onclick="do_search_grid('#grid','#search_form')">搜索</a>
        </form>
    </div>
    <!--右侧 表格-->
    <div data-options="region:'east',border:false,split:true" style="width:360px;">
        <table id="grid_time" data-options="fit:true,border:false">
        </table>
    </div>

    <!--中部 表格-->
    <div data-options="region:'center',border:false">
        <table id="grid" data-options="fit:true,border:false">
        </table>
    </div>
</div>

<!-- 工具栏 -->
<div id="tb" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="add()">添加</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="edit()">修改</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="del()">删除</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-application_xp_terminal" plain="true"
           onclick="excute()">执行</a>
    </div>
</div>
<!-- time工具栏 -->
<div id="tb_time" style="height:auto;padding-top:5px;width: 100%">
    <div style="margin-bottom:5px">
        <a href="#" class="easyui-linkbutton" iconCls="icon-cross" plain="true"
           onclick="clean_time()">清空日志</a>
    </div>
</div>
<div id="dlg_result" closed="true" class="easyui-dialog" title="执行结果" data-options="iconCls:'icon-save'"
     style="width:500px;height:380px;padding:10px">
    <textarea id="result" style="width:95%;height:95%"></textarea>
</div>
<!-- 编辑表单 -->
<div id="dlg" class="easyui-dialog" iconCls="icon-image_edit" style="width:530px;height:420px;padding:10px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post">
        <div style="display: none">
            <label>隐藏字段:</label>
            <input name="id" data-options="label:'id:'"/>
        </div>
        <div class="fitem">
            <input id="name" name="name" data-options="label:'任务名称:',required:true" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="cycle" data-options="label:'执行周期:',required:true" class="easyui-textbox" style="width:300px">
            <span>&nbsp;&nbsp;<a id="tip" href="#" title="" class="easyui-tooltip" style="color: blue">参数说明</a></span>
        </div>
        <div class="fitem">
            <input name="url" data-options="label:'命令网址:',required:true" class="easyui-textbox"
                   style="width:300px;">
        </div>
        <div class="fitem">
            <input name="ip" data-options="label:'ip定向:'" class="easyui-textbox" validType='checkIp'
                   style="width:300px;"> (指定命令网址中的主机IP 地址)
        </div>
        <div class="fitem">
            <input name="get_parm" multiline="true" data-options="label:'GET参数:'" class="easyui-textbox"
                   style="width:450px;height: 50px;">
        </div>
        <div class="fitem">
            <input name="post_parm" multiline="true" data-options="label:'POST参数:'" class="easyui-textbox"
                   style="width:450px;height: 50px;">
        </div>
        <div class="fitem">
            <div style="clear: both"></div>
            <div class="fitem" id="tip_content" style="display: none">
            <pre>
# Example of job definition:
# .---------------- minute (0 - 59)
# |  .------------- hour (0 - 23)
# |  |  .---------- day of month (1 - 31)
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
# |  |  |  |  |
# *  *  *  *  *

* 格式
* $task[] = "* * * * *"
* $task[] = "分 时 天 月 年"
*/
每分钟执行为 * * * * *
每3分钟执行为 */3 * * * *
每小时的第1分钟执行为 1 * * * *
每3小时的第1分钟执行为 1 */3 * * *
每天的凌晨1点0分执行为  0 1 * * *
每天的凌晨1点的每一分钟都执行为  * 1 * * *
每月1号的凌晨1点0分执行为 0 1 1 * *
            </pre>
            </div>
        </div>
        <div class="fitem">
            <input id="memo" name="memo" multiline="true" data-options="label:'备注:'"
                   class="easyui-textbox"
                   style="width:450px;height: 80px;">
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
        $('#tip').tooltip({
            position: 'right',
            content: $('#tip_content').html(),
            onShow: function () {
                $(this).tooltip('tip').css({
                    backgroundColor: '#F5F5F5',
                    //borderColor: '#E0E0E0'
                });
            }
        });
        var grid = $('#grid').datagrid({
            title: '',
            url: search_url,
            striped: true,
            rownumbers: true,
            pagination: true,
            singleSelect: false,
            idField: 'id',
            sortName: 'id',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            toolbar: '#tb',
            resizable: true,
            frozenColumns: [
                [
                    {field: 'ck', title: 'ID', checkbox: true, width: 120}
                ]
            ],
            columns: [
                [
                    {title: 'ID', field: 'id', sortable: true},
                    {title: '任务名称', field: 'name', sortable: true},
                    {title: '执行周期', field: 'cycle', sortable: true},
                    {title: '命令网址', field: 'url', sortable: true},
                    {title: 'ip定向', field: 'ip', sortable: true},
                    {
                        title: '最后执行', field: 'last_execute_time', sortable: true,
                        formatter: function (value, row, index) {
                            if (value) {
                                return unixtime2str(value).substr(2);
                            }
                        }
                    },
                    {
                        title: '添加时间', field: 'create_at', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substr(2);
                        }
                    },
                    {
                        title: '修改时间', field: 'update_at', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value).substr(2);
                        }
                    },
                    {title: '备注', field: 'memo', sortable: true}
                ]
            ],
            onClickRow: function (rowIndex, rowData) {
                //行点击事件
                $('#grid').datagrid("clearSelections");
                $('#grid').datagrid("selectRow", rowIndex);

                $('#grid_time').datagrid({url: search_time_url + "?id=" + rowData.id});
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
            }
        });

        //

        var grid_time = $('#grid_time').datagrid({
                title: '',
                striped: true,
                rownumbers: true,
                pagination: true,
                singleSelect: false,
                pageSize: 20,
                pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
                resizable: true,
                sortName: 'time',
                sortOrder: 'desc',
                toolbar: '#tb_time',
                columns: [
                    [
                        {
                            title: '执行时间', field: 'time', sortable: true,
                            formatter: function (value, row, index) {
                                if (value) {
                                    return unixtime2str(value).substr(2);
                                }
                            }
                        },
                        {title: '执行结果', field: 'result', sortable: true, width: 200}
                    ]
                ],
                onClickRow: function (rowIndex, rowData) {
                    //行点击事件
                }
                ,
                onDblClickRow: function (rowIndex, rowData) {
                    //行双击事件
                    $('#result').val(rowData.result);
                    $('#dlg_result').dialog('open');
                }
                ,
                onSelect: function (rowIndex, rowData) {
                    //行选择事件
                    if (rowData) {
                        //
                    }
                }
                ,
                onLoadSuccess: function (data) {
                    //加载或翻页后默认选中第一行
                    $(this).datagrid('scrollTo', 0);
                }
            })
            ;

    });
    function add() {
        $('#fm').form('clear');
        $('#dlg').dialog('open').dialog('setTitle', '新增');
        $('#db_port').numberbox('setValue', 3306);
        $('#db_type').combobox('setValue', '0');
    }

    function edit() {
        $('#fm').form('clear');
        var row = $('#grid').datagrid('getSelected');
        if (row) {
            $('#dlg').dialog('open').dialog('setTitle', '修改');
            $('#fm').form('load', row);
        }
        else{
            show_msg('请先选择一条记录.', 'error');
        }
    }

    function del() {
        var ids = [];
        var rows = $('#grid').datagrid('getSelections');
        for (var i = 0; i < rows.length; i++) {
            ids.push(rows[i].id);
        }
        if (rows) {
            $.messager.confirm('确认', '是否确认删除以下ID:' + ids, function (r) {
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
    function excute() {
        var ids = [];
        var node = $('#grid').datagrid('getSelected');
        if (node) {
            window.open(excute_url + '?id=' + node.id);
        }
    }
    function clean_time() {
        var row = $('#grid').datagrid('getSelected');
        var id = row.id;
        if (id) {
            $.messager.confirm('确认', '是否确认删除日志：任务名称:' + row.name, function (r) {
                if (r) {
                    $.post(clean_time_url, {id: id}, function (data) {
                        if (data.retcode == 0) {
                            show_msg(data.msg);
                            $('#grid_time').datagrid('reload'); // reload the user data

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