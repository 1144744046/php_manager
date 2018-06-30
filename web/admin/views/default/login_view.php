
<style>
    * {
    font-size: 14px;
        text-align: left;
    }
</style>
<?php
// echo phpinfo();
//?>
<div id="loginDialog" title="Welcome to firefly"
     style="display: none; width: 380px; height:230px;overflow: hidden;">
    <div id="loginTabs" class="easyui-panel" data-options="fit:true,border:false">
        <div style="padding: 10px;" align="center">
            <form method="post" class="form">
                <table cellpadding="8">
                    <tr>
                        <td>用户名：</td>
                        <td>
                            <input id="username" name="username" class="easyui-textbox"
                                   style="width:200px"/>
                        </td>
                    </tr>
                    <tr>
                        <td>密 码：</td>
                        <td>
                            <input name="password" class="easyui-passwordbox"
                                   style="width:200px"/>
                        </td>
                    </tr>
                    <tr>
                        <td>记住我：</td>
                        <td><input name="remember" type="checkbox" checked="checked"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    var last_login_username = read_cookie('last_login_username');
    $('#username').textbox('setValue', last_login_username);
    var loginFun = function () {
        var login_url = "<?php echo '/admin/login?referer=' . $redirect;?>";
        var loginTabs = $('#loginTabs');//当前选中的tab 加上 .tabs('getSelected')
        var $form = loginTabs.find('form');//选中的tab里面的form
        if ($form.length == 1 && $form.form('validate')) {
            msg_progress();
            //$('#loginBtn').linkbutton('disable');

            $.post(login_url, $form.serialize(), function (data) {
                msg_progress_close();
                //alert(JSON.stringify(data));
                if (data.retcode == 0) {
                    $.messager.alert('提示', data.msg, 'info', function () {
                        $('#loginBtn').linkbutton('enable');
                    });
                    create_cookie('last_login_username', $('#username').val(), 365);
                    //跳转到前台
                    go(data.data.url);
                    return false;
                } else {
                    $.messager.alert('提示', data.msg, 'error', function () {
                        $('#loginBtn').linkbutton('enable');
                    });
                }
            }, 'json');
        }
    };

    $('#loginDialog').show().dialog({
            modal: false,
            closable: false,
            iconCls: 'icon-user',
            buttons: [
                {
                    id: 'loginBtn',
                    text: '登录',
                    iconCls: 'icon-ok',
                    handler: function () {
                    loginFun();
                }
                }
            ],
            onOpen: function () {
        $('form :input:first').focus();
        $('form :input').keyup(function (event) {
            if (event.keyCode == 13) {
                loginFun();
            }
        });
    }
        });
    });
</script>