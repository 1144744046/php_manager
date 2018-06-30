/**
 * 创建cookies
 * createCookie("_fv", "1", 30, "/;domain=abc.com")
 */

function create_cookie(name, value, expire, path) {
    var d = path ? path : "/";
    if (expire) {
        var e = new Date;
        e.setTime(e.getTime() + 1e3 * 60 * 60 * 24 * expire);
        var f = "; expires=" + e.toGMTString()
    } else var f = "";
    document.cookie = name + "=" + value + f + "; path=" + d
}

/**
 * 读取cookies
 * @param a
 * @returns {*}
 */
function read_cookie(a) {
    for (var b = a + "=", c = document.cookie.split(";"), d = 0; d < c.length; d++) {
        for (var e = c[d];
             " " == e.charAt(0);) e = e.substring(1, e.length);
        if (0 == e.indexOf(b)) return e.substring(b.length, e.length)
    }
    return null
}

/**
 * //为了删除指定名称的cookie
 * @param name
 */
function del_cookie(name) {
    var date = new Date();
    date.setTime(date.getTime() - 10000);
    document.cookie = name + "=a; expires=" + date.toGMTString();
}

/**
 * 判断是否null
 * @param data
 */
function is_null(data) {
    return (data == "" || data == undefined || data == null) ? true : false;
}

/*
判断是否为数字
 */
function check_is_num(input) {
    if (is_null(input))
        return false;
    var re = /^[0-9]+.?[0-9]*$/;   //判断字符串是否为数字     //判断正整数 /^[1-9]+[0-9]*]*$/
    return re.test(input)
}

/**
 * //获取当前时间
 * @param name
 */
function current() {
    return (new Date()).Format("yyyy-MM-dd hh:mm:ss");
}

// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
Date.prototype.Format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

/*
 将文本转换成时间
 */
function str2date(str) {
    var d = null;
    var reg = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\.(\d+)$/
    if (arr = str.match(reg)) d = new Date(Number(arr[1]), Number(arr[2]) - 1, Number(arr[3]), Number(arr[4]), Number(arr[5]), Number(arr[6]), Number(arr[7]))
    return d;
}

/*
 将时间转换成文本
 */
function date2str(d) {
    var ret = d.getFullYear() + "-"
    ret += ("00" + (d.getMonth() + 1)).slice(-2) + "-"
    ret += ("00" + d.getDate()).slice(-2) + " "
    ret += ("00" + d.getHours()).slice(-2) + ":"
    ret += ("00" + d.getMinutes()).slice(-2) + ":"
    ret += ("00" + d.getSeconds()).slice(-2)
    return ret;
}

/*
 将时间戳转换成文本
 */
function unixtime2str(timestamp) {
    var d = new Date(parseInt(timestamp) * 1000);
    return date2str(d);
}

/**
 * var t =  parseInt(时间戳*1000);
 * new Date(t).format('yyyy-MM-dd');
 * 将时间戳按指定格式 格式化
 * @param format
 * @returns {*}
 */
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month
        "d+": this.getDate(), //day
        "h+": this.getHours(), //hour
        "m+": this.getMinutes(), //minute
        "s+": this.getSeconds(), //second
        "q+": Math.floor((this.getMonth() + 3) / 3), //quarter
        "S": this.getMilliseconds() //millisecond
    }
    if (/(y+)/.test(format)) format = format.replace(RegExp.$1,
        (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o) if (new RegExp("(" + k + ")").test(format))
        format = format.replace(RegExp.$1,
            RegExp.$1.length == 1 ? o[k] :
                ("00" + o[k]).substr(("" + o[k]).length));
    return format;
}


/**
 * 表单搜索
 * @param grid 加载的table
 * @param frm  搜索的表单
 */
function do_search_grid(grid, frm) {
    $(grid).datagrid('load', get_data_from_json(frm));
}

function do_search_tree(grid, frm) {
    $(grid).treegrid('load', get_data_from_json(frm));
}

/**
 * 把form下的字段转化成json
 * @param frm
 * @returns {{}}
 */
function get_data_from_json(frm) {
    var o = {};
    var a = $(frm).serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

/*
 弹出进度条
 */
function msg_progress(time, msg) {
    if (!$.isNumeric(time) || time < 1)
        time = 10 * 1000;
    if (!msg)
        msg = '正在加载中...';
    var win = $.messager.progress({
        title: '提示',
        msg: msg
    });
    setTimeout(function () {
        $.messager.progress('close');
    }, time)
}

/*
 隐藏进度条
 */
function msg_progress_close() {
    $.messager.progress('close');
}

/*
 消息提示
 msg:消息体   type:类型 error info question warning 默认为：info
 title: 默认为提示 timeout 默认关闭时间
 */
var icons = {
    "error": "messager-error",
    "info": "messager-info",
    "question": "messager-question",
    "warning": "messager-warning"
};

function show_msg(msg, type, title, timeout, w, h) {
    if (!title) title = '提示';
    if (!check_is_num(w)) w = 300;
    if (!check_is_num(h)) h = 120;

    if (check_is_num(timeout) || parseInt(timeout) > 0)
        timeout = parseInt(timeout);
    else
        timeout = 1200;
    var iconCls = icons[type] ? icons[type] : icons.info;
    msg = "<div class='messager-icon " + iconCls + "'></div>" + "<div>" + msg + "</div>";
    if (msg.length > 500) {
        w = 500;
        h = 300;
    }
    parent.$.messager.show({
        title: title,
        msg: msg,
        showType: 'slide',
        timeout: timeout,
        width: w,
        height: h,
        style: {
            right: '',
            top: document.body.scrollTop + document.documentElement.scrollTop,
            bottom: ''
        }
    });

}

/**
 * 格式字符串
 * var template1="我是{0}，今年{1}了";
 * var template2="我是{name}，今年{age}了";
 * var result1=template1.format("loogn",22);
 * var result2=template2.format({name:"loogn",age:22});
 * @param args
 * @returns {String}
 */
String.prototype.format = function (args) {
    var result = this;
    if (arguments.length > 0) {
        if (arguments.length == 1 && typeof (args) == "object") {
            for (var key in args) {
                if (args[key] != undefined) {
                    var reg = new RegExp("({" + key + "})", "g");
                    result = result.replace(reg, args[key]);
                }
            }
        }
        else {
            for (var i = 0; i < arguments.length; i++) {
                if (arguments[i] != undefined) {
                    var reg = new RegExp("({)" + i + "(})", "g");
                    result = result.replace(reg, arguments[i]);
                }
            }
        }
    }
    return result;
}

function openwinx(url, name, w, h) {
    if (!w) w = screen.width - 4;
    if (!h) h = screen.height - 95;
    window.open(url, name, "top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
}


function parseUrl(paras, url) {
    var url = arguments[1] ? arguments[1] : location.href;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {}
    for (i = 0; j = paraString[i]; i++) {
        paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
    }
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") {
        return "";
    } else {
        return returnValue;
    }
}

//生成链接文本
function html_anchor(url, text, tar, fun) {
    if (!text) {
        text = url;
    }
    if (!tar) {
        tar = '_self';
    }

    var html = '<a href="' + url + '" target="' + tar + '">' + text + '</a>';
    return html;
}

// 子iframe打开tab
function openTab(title, url) {
    // 如果父窗口存在在Tab打开，否转到链接
    if ($('#mainTabs', parent.document).length > 0) {
        window.parent.newTab(title, url);
    } else {
        window.location.href = url;
    }

}

//浏览器跳转
function go(url) {
    if (url) {
        window.location.href = url;
    }
}

Array.prototype.contains = function (needle) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}

//变换模板
var mainStyle = read_cookie('mainStyle');
//初始化模板控件
if (!mainStyle)
    mainStyle = "default";

//变换模板
function switch_styles(theme) {
    var link = $('#mainStyle');
    url = '/static/easyui/themes/' + theme + '/easyui.css';
    link.attr("href", url);
    create_cookie('mainStyle', theme, 365);
}

$(function () {
    switch_styles(mainStyle)
});

function array_out_repeat(a) {
    var hash = [], arr = [];
    for (var i = 0; i < a.length; i++) {
        hash[a[i]] != null;
        if (!hash[a[i]]) {
            arr.push(a[i]);
            hash[a[i]] = true;
        }
    }
    return arr;
}

Array.prototype.remove = function (val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};

function store_tree_close_nodes(tree_id, node_id) {
    var expand_nodes_ids = read_cookie("tree_state_" + tree_id);
    var ids = []
    if (expand_nodes_ids) {
        ids = expand_nodes_ids.split(',');
    }
    ids.remove(node_id);
    create_cookie("tree_state_" + tree_id, ids)
}

function store_tree_open_nodes(tree_id, node_id) {
    var expand_nodes_ids = read_cookie("tree_state_" + tree_id);
    var ids = [];
    if (expand_nodes_ids) {
        ids = expand_nodes_ids.split(',');
    }
    if (parseInt(node_id) > 0)
        ids.push(node_id);
    ids = array_out_repeat(ids);
    create_cookie("tree_state_" + tree_id, ids)
}

function store_tree_state(tree_id) {//参数为树的ID，注意不要添加#
    var expand_nodes_ids = [];
    var roots = $('#' + tree_id).tree('getRoots'), children, i, j;
    for (i = 0; i < roots.length; i++) {
        var children = $('#' + tree_id).tree('getChildren', roots[i].target);
        if (roots[i].state == 'open' && children.length > 0) {
            expand_nodes_ids.push(roots[i].id);
        }
        for (j = 0; j < children.length; j++) {
            var children2 = $('#' + tree_id).tree('getChildren', children[j].target);
            if (children[j].state == 'open' && children2.length > 0) {
                expand_nodes_ids.push(children[j].id);
            }
        }
    }
    create_cookie("tree_state_" + tree_id, expand_nodes_ids)
}

function restore_tree_state(tree_id) {
    var expand_nodes_ids = read_cookie("tree_state_" + tree_id)
    if (expand_nodes_ids) {
        var ids = expand_nodes_ids.split(',');
        for (var i = 0; i < ids.length; i++) {
            var node = $('#' + tree_id).tree('find', ids[i]);
            if (node) {
                $('#' + tree_id).tree('expand', node.target);
            }
        }
    }
}

function copy_to_clipboard(txt) {
    if (window.clipboardData) {
        window.clipboardData.clearData();
        window.clipboardData.setData("Text", txt);
        show_msg("已经成功复制到剪帖板上！");
    } else if (navigator.userAgent.indexOf("Opera") != -1) {
        window.location = txt;
    } else if (window.netscape) {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            show_msg("被浏览器拒绝！<br />请在浏览器地址栏输入'about:config'并回车<br />然后将'signed.applets.codebase_principal_support'设置为'true'");
        }
        var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
        if (!clip) return;
        var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
        if (!trans) return;
        trans.addDataFlavor('text/unicode');
        var str = new Object();
        var len = new Object();
        var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
        var copytext = txt;
        str.data = copytext;
        trans.setTransferData("text/unicode", str, copytext.length * 2);
        var clipid = Components.interfaces.nsIClipboard;
        if (!clip) return false;
        clip.setData(trans, null, clipid.kGlobalClipboard);
        show_msg("已经成功复制到剪帖板上！");
    }
}

String.prototype.stripHTML = function () {
    var reTag = /<(?:.|\s)*?>/g;
    return this.replace(reTag, "");
}