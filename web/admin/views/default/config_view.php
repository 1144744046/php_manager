<script>
    var search_url = '/admin/config/search';
    var save_url = '/admin/config/save';
    var del_url = '/admin/config/del';

    var menu_url = '/admin/config_menu/get_all_menu';
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
    <div data-options="region:'west',split:true,border:false" style="width:200px;">
        <table id="admin_config_menu" data-options="fit:true,border:false">
        </table>
    </div>

    <!--中间 表格-->
    <div data-options="region:'center',split:true,border:false" title="参数列表">
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
    </div>
</div>

<!-- 编辑表单 -->
<div id="dlg" class="easyui-dialog" iconCls="icon-image_edit" style="width:520px;height:460px;padding:10px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" style="padding-top: 20px;">
        <div style="display: none">
            <label>隐藏字段:</label>
            <input name="id" data-options="label:'id:'"/>
            <input name="menuid" data-options="label:'menuid:'"/>
        </div>
        <div class="fitem">
            <input name="listorder" data-options="label:'排序值:',required:true" class="easyui-numberbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="alias" data-options="label:'别名:',required:true" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input id="name" name="name" data-options="label:'名称:',required:true" class="easyui-textbox"
                   style="width:300px">
        </div>
        <div class="fitem">
            <input name="value" data-options="label:'参数值:',required:true" multiline="true" class="easyui-textbox"
                   style="width:450px;height: 100px;">
        </div>
        <div class="fitem">
            <input name="memo" data-options="label:'备注:'" multiline="true" class="easyui-textbox"
                   style="width:450px;height: 100px;">
        </div>
        <div class="fitem">
            <input id='upload_img' name="upload_img" data-options="label:'图片上传:'" class="easyui-textbox"
                   style="width:300px;">&nbsp;&nbsp;<input type="button" id="upload_upload_img"
                                                           value="上传图片"/>&nbsp;<font color="red">(不保存数据库)</font>
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

        KindEditor.ready(function (K) {
            var editor = K.editor({
                allowFileManager: false,
                uploadJson: '/admin/up_file/up/image'
            });
            K('#upload_upload_img').click(function () {
                editor.loadPlugin('image', function () {
                    editor.plugin.imageDialog({
                        imageUrl: K('#upload_img').val(),
                        clickFn: function (url, title, width, height, border, align) {
                            $('#upload_img').textbox("setValue", url);
                            editor.hideDialog();
                        }
                    });
                });
            });
        });

        $('#admin_config_menu').tree({
            title: '',
            url: menu_url,
            parentField: 'parentid',
            animate: true,
            lines: true,
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
                $(this).tree('expand', node.target);
                //同步显示数据
                $('#grid').datagrid({
                    queryParams: {
                        menuid: node.id
                    }
                });

            },
            onExpand: function (node) {
                store_tree_state('admin_config_menu');
            },
            onCollapse: function (node) {
                store_tree_state('admin_config_menu');
            },
            onLoadSuccess: function (node, data) {
                restore_tree_state('admin_config_menu');
            }
        });

        var grid = $('#grid').datagrid({
            url: search_url,
            striped: true,
            rownumbers: true,
            pagination: false,
            singleSelect: true,
            idField: 'listorder',
            sortName: 'listorder',
            sortOrder: 'desc',
            pageSize: 20,
            pageList: [10, 20, 30, 40, 50, 100, 200, 300, 400, 500],
            toolbar: '#tb',
            resizable: true,
            columns: [
                [
                    {title: 'ID', field: 'id', sortable: true},
                    {title: '排序', field: 'listorder', sortable: true},
                    {title: '别名', field: 'alias', sortable: true},
                    {title: '参数名', field: 'name', sortable: true},
                    {title: '参数值', field: 'value', sortable: true},
                    {title: '备注', field: 'memo', sortable: true}
                ]
            ],
            onClickRow: function (rowIndex, rowData) {
                //行点击事件
                $('#grid').datagrid("clearSelections");
                $('#grid').datagrid("selectRow", rowIndex);
            },
            onDblClickRow: function (rowIndex, rowData) {
                //行双击事件
                edit();
            },
            onSelect: function (rowIndex, rowData) {
                //行选择事件
                if (rowData) {
                }
            },
            onLoadSuccess: function (data) {
                //加载或翻页后默认选中第一行
                $(this).datagrid('scrollTo', 0);
                $(this).datagrid('selectRow', 0);
            }
        });
    });

    function add() {
        $('#fm').form('clear');
        var row = $('#admin_config_menu').tree('getSelected');
        if (row) {
            if (row.islast != 1) {
                show_msg('请终极分类添加参数.');
                return false;
            }
            $('#fm').form('load', {
                menuid: row.id,
                listorder: 50
            });

            $('#name').textbox('enable');
            $('#dlg').dialog('open').dialog('setTitle', '新增');
        }
        else {
            show_msg('请先在左侧选择参数所属分类.');
        }
    }

    function edit() {
        $('#fm').form('clear');
        var row = $('#grid').datagrid('getSelected');
        if (row) {
            $('#dlg').dialog('open').dialog('setTitle', '修改');
            $('#fm').form('load', row);
            $('#name').textbox('disable');
        }
        else {
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