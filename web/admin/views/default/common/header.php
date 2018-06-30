<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<!--    <title>--><?php //echo sys_config_get('SYS_NAME'); ?><!-- - --><?php //echo sys_config_get('SYS_VERSION'); ?><!--</title>-->
    <link id="mainStyle" rel="stylesheet" type="text/css" href="/static/easyui/themes/default/easyui.css">
    <link id="mainStyle" rel="stylesheet" type="text/css" href="/static/easyui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="/static/easyui/themes/icon.css">
    <script type="text/javascript" src="/static/easyui/jquery.min.js"></script>
    <script type="text/javascript" src="/static/easyui/jquery.easyui.min.js"></script>
    <script src="/static/easyui/locale/easyui-lang-zh_CN.js" type="text/javascript"></script>
    <!-- 自定义开始 -->
    <link href="/static/easyui/plus/common.css" rel="stylesheet" type="text/css"/>
    <link href="/static/easyui/plus/icons/icon.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/easyui/plus/common.js"></script>
    <script type="text/javascript" src="/static/easyui/plus/validator.js"></script>

    <!-- 美化
    <link href="/static/easyui/insdep/easyui_full.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/easyui/insdep/insdep-extend.min.js"></script>
    -->

    <!-- editor-->
    <link rel="stylesheet" href="/static/kd/themes/default/default.css"/>
    <script src="/static/kd/kindeditor-all-min.js"></script>
</head>
<body>
<script>
    var sys_name ="<?php echo sys_config_get('SYS_NAME'); ?>//";
    document.writeln("<div id='show_load_layers' style='position:fixed;left: 0px; top: 0px; width:1200px; height: 1200px; background-color: #fff; z-index: 0; padding: 16px; '>视图加载中，请稍后...</div>")

    $(function(){
        $("#show_load_layers").hide();
    });
</script>