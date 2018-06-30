var mainMenu;
var mainTabs;

$(function () {
    //屏蔽右键
    $(document).ready(function () {
        $(document).bind("contextmenu", function (e) {
            return false;
        });

    });

//----------------------------Main Menu----------------------------
    var mainMenu = $('#main_menu').tree({
        data: menu,
        parentField: 'parentid',
        animate: true,//树展开时，滚动效果
        lines: true,//显示树的行虚线
        loadFilter: function (data) {
            var root = [];
            if (sys_name)
                root_node_name = sys_name;
            else
                root_node_name = '根结点';
            var node = {id: 0, text: root_node_name, state: 'open', iconCls: 'icon-house'};
            node.children = data;
            root.push(node);
            return root;
        },
        onClick: function (node) {
            if (node.s && node.c && node.m) {

                //组装Title,加上父结点名称,便于识别，及防止title重名
                var title = '';
                var o = $(this).tree("getParent", node.target)
                while (o) {
                    if (o.text) {
                        title += o.text + "-";
                    }
                    o = $(this).tree("getParent", o.target)
                    if (o && o.id == 0) {
                        break;
                    }
                }
                title += node.text;
                //组装URL
                var url = "/" + node.s + "/" + node.c + "/" + node.m;

                //打开页面
                newTab(title, url, node.icon);
            } else {
                //展开节点
                $(this).tree("expand", node.target)
            }

        },
        onDblClick: function (node) {
            $(this).tree('collapseAll', node.target)
        },
        onExpand: function (node) {
            store_tree_state('main_menu');
        },
        onCollapse: function (node) {
            store_tree_state('main_menu');
        },
        onLoadSuccess: function (node, data) {
            restore_tree_state('main_menu');
        },
        formatter: function (node) {//格式化结果，显示子菜单数量
            var s = node.text;
            if (node.children && node.children.length > 0) {
                s += '&nbsp;<span style=\'color:grey\'>(' + node.children.length + ')</span>';
            }
            return s;
        }
    });
//----------------------------Main Tab----------------------------
    //绑定tabs的右键菜单
    $("#mainTabs").tabs({
        onContextMenu: function (e, title) {
            $('#mainTabsMenu').menu('show', {
                left: e.pageX,
                top: e.pageY
            }).data("tabTitle", title);
            if (document.all) { //判断IE浏览器
                window.event.returnValue = false;
                e.returnValue = false;
                return false;
            }
            else {
                e.preventDefault();
            }
            ;
        }
    });

    mainTabs = $('#mainTabs').tabs({
        onAdd: function (title, index) {
        },
        onBeforeClose: function (title, index) {
            //预留,注销iframe
            var tab_iframe_name = "iframe_" + title;
            try {
                var frame = $("#" + tab_iframe_name);
                frame.remove();
                //回收内存
                $.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
                if ($.browser.msie) {
                    CollectGarbage();
                }
            } catch (e) {
            }

        }
    });

    //实例化menu的onClick事件
    $("#mainTabsMenu").menu({
        onClick: function (item) {
            CloseTab(this, item.name);
        }
    });


//几个关闭事件的实现

    function CloseTab(menu, type) {
        if (menu != '')
            var curTabTitle = $(menu).data("tabTitle");
        else
            var curTabTitle = $("#mainTabs").tabs('getSelected').panel("options").title;
        var tabs = $("#mainTabs");

        if (type === "close") {
            tabs.tabs("close", curTabTitle);
            return;
        }

        var allTabs = tabs.tabs("tabs");
        var closeTabsTitle = [];

        $.each(allTabs, function () {
            var opt = $(this).panel("options");
            if (opt.closable && opt.title != curTabTitle && type === "Other") {
                closeTabsTitle.push(opt.title);
            } else if (opt.closable && type === "All") {
                closeTabsTitle.push(opt.title);
            }
        });

        for (var i = 0; i < closeTabsTitle.length; i++) {
            tabs.tabs("close", closeTabsTitle[i]);
        }
    }

    $("#mainTabs_jumpIe").click(function () {
        try {
            var t = $("#mainTabs").tabs('getSelected').panel("options").title;
            var tab_iframe_name = "iframe_" + t;
            var url = $('#' + tab_iframe_name).attr("src");
            if (url != '' && url.length > 1) window.open(url);
        } catch (e) {
        }
    });
    $("#mainTabs_closeOther").click(function () {
        CloseTab('', 'Other');
    });
    $("#mainTabs_closeAll").click(function () {
        CloseTab('', 'All');
    });

//----------------------------Themes----------------------------
    var themes = [
        {value: 'default', text: '蓝色', group: '默认'},
        {value: 'gray', text: '灰色', group: '默认'},
        {value: 'black', text: '黑色', group: '默认'},
        {value: 'metro', text: 'Metro', group: '默认'},
        {value: 'bootstrap', text: 'Bootstrap', group: '默认'}
        /*
         {value: 'metro-blue', text: 'Metro Blue', group: 'Metro'},
         {value: 'metro-gray', text: 'Metro Gray', group: 'Metro'},
         {value: 'metro-green', text: 'Metro Green', group: 'Metro'},
         {value: 'metro-orange', text: 'Metro Orange', group: 'Metro'},
         {value: 'metro-red', text: 'Metro Red', group: 'Metro'},
         {value: 'ui-cupertino', text: 'Cupertino', group: 'UI'},
         {value: 'ui-dark-hive', text: 'Dark Hive', group: 'UI'},
         {value: 'ui-pepper-grinder', text: 'Pepper Grinder', group: 'UI'},
         {value: 'ui-sunny', text: 'Sunny', group: 'UI'}
         */
    ];

    //变换模板
    var mainStyle = read_cookie('mainStyle');

    //变换模板
    function switch_styles(theme) {
        var link = $('#mainStyle');
        var url = '/static/easyui/themes/' + theme + '/easyui.css';
        link.attr("href", url);
        create_cookie('mainStyle', theme, 365);
    }

    //切换模板
    function onChangeTheme(theme) {
        switch_styles(theme)
    }

    //初始化模板控件
    if (!mainStyle)
        mainStyle = "default";
    $('#cb-theme').combobox({
        groupField: 'group',
        data: themes,
        editable: false,
        panelHeight: 'auto',
        onChange: onChangeTheme,
        onLoadSuccess: function () {
            $(this).combobox('setValue', mainStyle);
        }
    });

    //显示时间控件
    var timer_span = $("#timer_span"), change_time_span = function () {
        timer_span.text(current());
    };
    change_time_span();
    window.setInterval(change_time_span, 1000);

});

function newTab(title, url, iconCls) {
    var tab_iframe_name = "iframe_" + title;
    if ($('#mainTabs').tabs('exists', title)) {
        $('#mainTabs').tabs('select', title);
        $("#" + tab_iframe_name).attr("src", url);
    } else {
        $('#mainTabs').tabs('add', {
            title: title,
            iconCls: iconCls,
            closable: true,
            content: '<iframe id="' + tab_iframe_name + '" name="' + tab_iframe_name
            + '" src="' + url + '" allowTransparency="true" style="border:0;width:100%;height:'
            + ($('#mainTabs').height() - 35) + 'px;" frameBorder="0"></iframe>',
            tools: [
                {
                    iconCls: 'icon-mini-refresh',
                    handler: function () {
                        $("#" + tab_iframe_name).attr("src", url);
                    }
                }
            ]
        });
    }
}

/**
 * 关闭tab
 * @param title
 * @param top_iframe_title 要刷新的iframe
 */
function closeTab(title, top_iframe_title) {
    $('#mainTabs').tabs('close', title);
}
