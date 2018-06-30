<script>
    //菜单数据
    var menu =<?php echo json_encode($this->menus);?>;
</script>
<script type="text/javascript" src="/static/easyui/plus/index.js"></script>
<link rel="stylesheet" type="text/css" href="/static/easyui/plus/index.css">
<style type="text/css">
    .tree-node-selected {
        /* color: #FFFFFF;*/
    }
</style>
<div id="mainLayout" class="easyui-layout hidden" data-options="fit: true">
    <div id="north" data-options="region:'north'" style="height: 50px;line-height: 45px;">
        <div style="float: left;width:190px;">
            <span style="font-size: 18px;font-weight: bold;padding-left: 10px;"><?php echo sys_config_get('SYS_NAME') ?></span>
        </div>
        <div style="float: right">
            <span style="color: red;font-weight: bold">欢迎您,<?php echo $this->G['username']; ?>.</span>
            <span id="timer_span"></span>
            <?php echo $report;?>
            <span id="themeSpan">
                <span>风格：</span>
                <select id="cb-theme" style="width:80px;height: 30px;" class="easyui-combobox" data-options="editable:false"></select>
            </span>
            <span>
                    <a id="btnExit" class="easyui-linkbutton" data-options="plain: true, iconCls: 'icon-door_open'"
                       onclick="window.location.href='/admin/login/logout'">退出系统</a>
                    <a id="btnShowNorth" class="easyui-linkbutton"
                       data-options="plain: true, iconCls: 'layout-button-down'"
                       style="display: none;"></a>
                </span>
        </div>
        <div style="clear: both"></div>
    </div>
    <div data-options="region:'west',split:true,iconCls: 'icon-application_view_tile'" title="菜单导航栏"
         style="width:200px;padding-top:6px;padding-left:6px;">
        <ul id="main_menu" class="easyui-tree"></ul>
    </div>

    <div data-options="region: 'center',split:true">
        <div id="mainTabs_tools" class="tabs-tool">
            <table style="margin: 0">
                <tr>
                    <td><a id="mainTabs_jumpIe" class="easyui-linkbutton easyui-tooltip" title="在新页面中打开该选项卡"
                           data-options="plain: true, iconCls: 'icon-application'"></a></td>
                    <td>
                        <div class="datagrid-btn-separator"></div>
                    </td>
                    <td><a id="mainTabs_closeOther" class="easyui-linkbutton easyui-tooltip" title="关闭除当前选中外的其他所有选项卡"
                           data-options="plain: true, iconCls: 'icon-application_form_delete'"></a></td>
                    <td>
                        <div class="datagrid-btn-separator"></div>
                    </td>
                    <td><a id="mainTabs_closeAll" class="easyui-linkbutton easyui-tooltip" title="关闭所有选项卡"
                           data-options="plain: true, iconCls: 'icon-cancel'"></a></td>
                </tr>
            </table>
        </div>
        <div id="mainTabs" class="easyui-tabs"
             data-options="fit: true, border: false, tools: '#mainTabs_tools'">
            <div id="homePanel" data-options="title: '主页', iconCls: 'icon-house'">
                <div id="main_welcome" class="easyui-layout" data-options="fit: true,closable:false">
                    <!-- 后台主页-->
                    <div style="margin:20px">Welcome,<?php echo sys_config_get('SYS_NAME') ?>.</div>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="mainTabsMenu" class="easyui-menu" style="width:120px;">
    <div name="close">关闭</div>
    <div name="Other">关闭其他</div>
    <div name="All">关闭所有</div>
</div>
