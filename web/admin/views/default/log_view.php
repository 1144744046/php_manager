<script>
    var search_url = '/admin/log/search';
    var del_url = '/admin/log/del';
    var clean_url = '/admin/log/clean';
</script>

<!-- 页面布局 -->
<div id="mainLayout" class="easyui-layout hidden" data-options="fit: true">
    <!--头部 搜索-->
    <div data-options="region:'north',border:false" style="padding:5px;border-bottom: 1px solid #CCCCCC">
        <form id="search_form" name="search_form">
            用户名: <input name="username" class="easyui-textbox" style="width:80px">
            模块: <input name="s" class="easyui-textbox" style="width:80px">
            类名: <input name="c" class="easyui-textbox" style="width:80px">
            方法: <input name="m" class="easyui-textbox" style="width:80px">
            参数: <input name="data" class="easyui-textbox" style="width:80px">
            &nbsp;
            <a href="#" class="easyui-linkbutton" iconCls="icon-search"
               onclick="do_search_grid('#grid','#search_form')">搜索</a>
        </form>
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
        <a href="#" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="del()">删除</a>
        <a href="#" class="easyui-linkbutton" iconCls="icon-stop" plain="true" onclick="clean()">清空</a>
    </div>
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
            idField: 'username',
            sortName: 'time',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            toolbar: '#tb',
            resizable: true,
            frozenColumns: [
                [
                    {field: 'ck', title: 'username', checkbox: true, width: 120}
                ]
            ],
            columns: [
                [
                    {title: '用户名', field: 'username', sortable: true},
                    {title: '模块', field: 's', sortable: true},
                    {title: '类名', field: 'c', sortable: true},
                    {title: '方法', field: 'm', sortable: true},
                    {
                        title: '参数', field: 'data', sortable: true,
                        formatter: function (value, row, index) {
                            var v = str = eval("'" + value + "'");
                            return v;
                        }
                    },
                    {title: 'ip', field: 'ip', sortable: true},
                    {
                        title: '时间', field: 'time', sortable: true,
                        formatter: function (value, row, index) {
                            return unixtime2str(value);
                        }
                    }
                ]
            ],
            onClickRow: function (rowIndex, rowData) {
                //行点击事件
                $('#grid').datagrid("clearSelections");
                $('#grid').datagrid("selectRow", rowIndex);
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

    });

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

    function clean() {
        $.messager.confirm('确认', '是否确认清空所有日志', function (r) {
            if (r) {
                $.post(clean_url, {}, function (data) {
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
</script>