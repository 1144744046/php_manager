<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>欢迎来到萤火虫</title>
    <link type="text/css" rel="stylesheet" href="/static/css/csl_global.css"/>
</head>
<body>
<div class="header">
    <div class="yhc-grid">
        <div class="logo">
            <a href="<?php echo base_url();?>" class="logo-img">
                <img src="<?php echo sys_config_get("SYS_DEFAULT_DOMAIN").sys_config_get("SYS_LOGO_PIC")?>"
                style="width: 127px;height: 64px;">
            </a>
            <span class="logo-title">欢迎登录</span>
        </div>
    </div>
</div>

<div class="content">
    <div class="yhc-content-grid">


        <div class="c-right">
            <div class="account-login"  style="display: block;">
                <div class="form-wrap">
                    <div class="login-tabs">
                        <div class="login-header">
                            <ul class="yhc-tabs-nav">
                                <ol class="scan"  id="scan">
                                    <a href="javascript:void(0)">
                                    <span class="scan-right">
                                        <img src="/static/images/scan.png">
                                    </span>
                                    </a>
                                </ol>
                                <li class="yhc-login-by-password yhc-tabs-active">
                                    <a>
                                        账号密码登录
                                    </a>
                                </li>
                                <li class="yhc-login-by-phone ">
                                    <a>
                                        手机动态登录
                                    </a>
                                </li>
                            </ul>
                            <div class="line"></div>
                        </div>
                        <div class="login-pannel">
                            <div class="login-password-pannel">
                                <div class="yhc-tips-danger" id="error-password">手机号</div>
                                <form id="login-form" class="yhc-form">
                                    <div class="MessageReg-fspix">
                                        <input type="text" placeholder="请输入常用的手机号" class="yhc-input" id="phone" maxlength="11">
                                    </div>
                                    <div class="MessageReg-fspix">
                                        <div class="input-left-img"><img src="/static/images/password_reg.png"></div>
                                        <input type="password"  placeholder="密码(6-16位字母/数字/字符)" class="yhc-input" id="pass"
                                               maxlength="16" onblur="passwordBlur()">
                                        <input type="text" id="text" placeholder="密码(6-16位字母/数字/字符)" class="yhc-input"
                                               maxlength="16" style="display: none" onblur="textBlur()">
                                        <em class="eye" title="显示密码">
                                            <img src="/static/images/eye-open.png" id="eye">
                                        </em>
                                    </div>
                                    <section class="MessageReg-fspix">
                                    <button class="agreement-btn">登录</button>
                                    </section>
                                </form>
                            </div>
                            <div class="login-phone-pannel" style="display: none;">
                                <div class="yhc-tips-danger" id="error-phone"></div>
                                <form id="login-form" class="yhc-form">
                                    <div class="MessageReg-fspix">
                                        <input type="text" placeholder="请输入常用的手机号" class="yhc-input" id="phone-pannel" maxlength="11">
                                    </div>
                                    <div class="MessageReg-fspix">
                                        <input type="text" placeholder="验证码" class="yhc-input" id="code" maxlength="6">
                                        <a  class="count-button">获取验证码</a>
                                    </div>
                                    <div class="MessageReg-fspix">
                                      <button class="agreement-btn">登录</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="other-ways-login">
                    <div class="authorize-login">
                        <p>使用第三方登录</p>
                        <span class="register-new-account">
                        <a class="" href="user_center/register">注册新账号</a>
                    </span>
                        <ul class="authorize-login-list">
                            <li class="authorize-login-item">
                                <a title="微信登录" class="wechat-login"></a>
                            </li>
                            <li class="authorize-login-item">
                                <a title="QQ登录" class="qq-login"></a>
                            </li>
                            <li class="authorize-login-item">
                                <a title="微博登录" class="weibo-login"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="app-scan-login" style="display: none;">

                <div class="scan-content login-tabs">
                    <div class="login-header scan-login-header" >
                        app扫码注册
                    </div>
                    <div class="scan-title" id="app-scan">
                        <img src="/static/images/app-scan.png" />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-3.3.1.min.js"></script>
<script>
    $(document).ready(function() {
        var eye_open=true;
        $("input").focus(function(){
            $("input",this).css("border-color","#2395ff");
        });
        $("input").blur(function(){
            $("input",this).css("border-color","#ddd");
        });
        //获取短信，倒计时
        $(".count-button").click(function(){
            var phone=$("#phone-pannel").val();
            if(phone!=""){
                if(/^1[34578]\d{9}$/.test(phone)){
                    $("#error-phone").html("");
                }else{
                    $("#error-phone").html("!-请输入正确的手机号");
                }
            }
        });
        //手机号码
        $("#phone-pannel").keyup(function(){
            var phone=$("#phone-pannel").val();
            if(phone!=""){
                if(/^1[34578]\d{9}$/.test(phone)){
                    $("#error-phone").html("");
                }
            }
        });
      ///用户名密码登录
        $(".yhc-login-by-password").click(function(){
            $(".yhc-login-by-phone").removeClass("yhc-tabs-active")
            $(this).addClass("yhc-tabs-active");
            $(".line").css("left","");
            $(".login-password-pannel").css("display","block");
            $(".login-phone-pannel").css("display","none");
        });
        //快捷方式登录
        $(".yhc-login-by-phone").click(function(){
            $(".yhc-login-by-password").removeClass("yhc-tabs-active")
            $(this).addClass("yhc-tabs-active");
            $(".line").css("left","53%");
            $(".login-password-pannel").css("display","none");
            $(".login-phone-pannel").css("display","block");
        });
        //眼睛点击，是否显示密码
        $(".eye").click(function(){
            $("#pass").toggle();
            $("#text").toggle();
            if(eye_open){
                $("#eye").attr('src','/static/images/eye-close.png');
                eye_open=false;
            }else{
                $("#eye").attr('src','/static/images/eye-open.png');
                eye_open=true;
            }
        });
        $("#scan").click(function(){
            $(".app-scan-login").css("display","block");
            $(".account-login").css("display","none");
        });
        $("#app-scan").click(function(){
            $(".app-scan-login").hide();
            $(".account-login").show();
        });

    });
    //text框失去焦点把text框的值赋值给password框
    function textBlur() {
        $("#pass").val($("#text").val());
    }

    //password框失去焦点把password框的值赋值给text框
    function passwordBlur() {
        $("#text").val($("#pass").val());
    }
</script>
</body>
</html>