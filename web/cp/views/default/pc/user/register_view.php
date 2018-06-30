<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>欢迎来到萤火虫</title>
    <link TYPE="text/css" rel="stylesheet" href="/static/css/common.css">
    <link TYPE="text/css" rel="stylesheet" href="/static/css/csl_global.css">


    <style>

    </style>
</head>
<body>
<div class="header">
    <div class="yhc-grid">
        <div class="logo">
            <a href="<?php echo base_url();?>" class="logo-img">
                <img src="<?php echo sys_config_get("SYS_DEFAULT_DOMAIN").sys_config_get("SYS_LOGO_PIC")?>"
                     style="width: 127px;height: 64px;">
            </a>
            <span class="logo-title">新用户注册</span>
        </div>
    </div>
</div>

<div class="content">
    <div class="yhc-content-grid">


        <div class="c-right">
            <div class="reg-tabs">
                <div class="reg-header">
                    手机注册
                </div>

                <div class="reg-pannel">
                    <div class="yhc-tips-danger" id="error"></div>
                    <form id="reg-form" class="yhc-form">
                        <section class="MessageReg-fspix">
                            <input type="text" placeholder="请输入常用的手机号" class="yhc-input" id="phone" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')">
<!--                            <div class="count-button" id="countBtn">获取验证码</div>-->
                            <input type="button" id="countBtn" class="regGetcodeBtn count-button" value="获取动态码">
                        </section>

                        <section class="MessageReg-fspix">
                            <input type="text" placeholder="验证码" class="yhc-input" id="code" maxlength="4"  onkeyup="this.value=this.value.replace(/\D/g,'')">
                        </section>

                        <section class="MessageReg-fspix">
                            <div class="input-left-img"><img src="/static/images/password_reg.png"></div>
                            <input type="password"  placeholder="密码(6-16位字母/数字/字符)" class="yhc-input" id="pass"
                                   maxlength="16" minlength="6" onblur="passwordBlur()">
                            <input type="text" id="text" placeholder="密码(6-16位字母/数字/字符)" class="yhc-input" id="pass"
                                   maxlength="16" minlength="6" style="display: none" onblur="textBlur()">
                            <em class="eye" title="显示密码">
                                <img src="/static/images/eye-open.png" id="eye">
                            </em>
                        </section>
                        <section class="MessageReg-fspix">
                            <input type="button" value="注册" id="agreementBtn" class="agreement-btn">
                        </section>

                        <div class="reg-agreement">
                            <input type="checkbox" id="reg-agreement-checkbox" checked="checked"><span>我已阅读和同意<a href="">萤火虫网服务协议</a></span>
                        </div>

                        <div class="other-ways">
                            <span>已有账号?<a href="../login">登录</a></span>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="regValidcodeBox" class="reg-validcode-box" style="display: none;">
    <div class="reg-validcode-tipTextBox">
        <span class="reg-validcode-tipText reg-tipText" id="regValidcodeTipText">
            请填写图片验证码
        </span>
    </div>
    <div class="reg-validcode-inputBox">
        <input type="text" placeholder="验证码" class="yhc-input reg-code-input" id="yzm_code" maxlength="4">

        <img id='regValidImg' width='80' height='40' src='/cp/api/yzm_api/get_yzm?t='+new Date().getTime() onclick="this.src='/cp/api/yzm_api/get_yzm?t='+new Date().getTime()" />
    </div>
    <div class="reg-validcode-btnBox">
        <input type="button" value="确定" id="regValidcodeImgBtn" class="reg-validcode-btn reg-validcode-btnOn" autocomplete="off">
    </div>
    <div class="reg-validcode-close" id="regValidcodeClose"></div>
</div>
<div class="reg-validcodeBg" id="regValidcodeBg" style="display: none;"></div>
<script type="text/javascript" src="/static/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript">

    $(document).ready(function() {
        var eye_open=true;
        //输入框获取焦点和失去焦点时，边框的改变
        $("input").focus(function(){
            $("input",this).css("border-color","#2395ff");
        });
        $("input").blur(function(){
            $("input",this).css("border-color","#ddd");
        });
        $("#regValidcodeClose").click(function(){
            $("#regValidcodeBox").hide();
            $("#regValidcodeBg").hide();
        });
        //注册密码是否可见
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
        //获取注册短信验证码被点击, //点击获取短信验证码
        $("#countBtn").click(function(){
            var phone=$("#phone").val();
            if(/^1[34578]\d{9}$/.test(phone)){
                $("#error").html("");
                $("#regValidcodeBox").show();
                $("#regValidcodeBg").show();
                $("#regValidImg").click();
                $("#yzm_code").val("");
                $("#regValidcodeTipText").text("请填写图片验证码");
            }else{
                $("#error").html("请输入正确的手机号");
            }
        });
        //获取验证码确定
        $("#regValidcodeImgBtn").click(function(){
            var yzm_code=$("#yzm_code").val();
            var phone=$("#phone").val();
            if(yzm_code.length!=4){
                return;
            }
            if(!check_text(yzm_code)){
                return;
            }
            ajax_get_code(phone,yzm_code);
        });
        //实时监听输入框值得变化
        $(".reg-pannel :input").on("input propertychange",function () {
            var $parent = $(this).parent().parent();
            var reg="^(\\d+[A-Za-z]|[A-Za-z0-9]*)|([A-Za-z]+\\d|[A-Za-z0-9]*)$";
            if ($(this).is('#phone')) {
                if(!(/^1[34578]\d{9}$/.test(this.value))){
                    $("#error").html("请输入正确的手机号");
                }else{
                    $("#error").html("");
                    //判断手机号是否注册了
                    is_mobile_reg(this.value);
                }
            }
            if ($(this).is('#pass')) {
                if (this.value.length < 6||!this.value.match(reg)) {
                    $("#error").html("密码长度为6-16");
                } else {
                    $("#error").html("");
                }
            }
            if ($(this).is('#code')) {
                if (this.value.length < 4) {
                    $("#error").html("短信验证码长度为4");
                }else{
                    $("#error").html("");
                }
            }
        });
        //注册按钮点击
       $("#agreementBtn").click(function(){
           var phone=$("#phone").val();
           if(!(/^1[34578]\d{9}$/.test(phone))){
               $("#error").html("请输入正确的手机号");
               return;
           }
           var code=$("#code").val();

           if(code.length!=4){
               $("#error").html("短信验证码长度为4");
               return;
           }
           var pass=$("#pass").val();
           if(pass.length<6||pass.length>16){
               $("#error").html("密码长度为6-16");
               return;
           }
           var ischeck=$("#reg-agreement-checkbox").is(':checked');
           if(!ischeck){
               $("#error").html("未同意协议");
               return;
           }
          $("#error").html("");
           post_register(phone,code,pass);
       });
    });
    //获取验证码倒数60秒
    var common = {
        node:null,
        count:60,
        start:function(){
            if(this.count > 0){
                this.node.css("color","#ccc");
                this.node.val(this.count--+"秒后重新获取") ;
                var _this = this;
                setTimeout(function(){
                    _this.start();
                },1000);
            }else{
                this.node.css("color","#2395ff");
                this.node.removeAttr("disabled");
                this.node.val("重新获取");
                //60秒读完，变回开始背景颜色，在这里添加。
                this.count = 60;
                this.node.bind("click");
            }
        },
        //初始化
        init:function(node){
            this.node = node;
            this.node.attr("disabled","true");
            this.start();
        }
    };


    //get请求，发送短信验证码
    function ajax_get_code(phone,yzm_code){
        $.ajax({
            url: "/cp/api/user/get_code",
            data:{mobile:phone,yzm_code:yzm_code},
            type: "GET",
            dataType:'json',
            success:function(result){
                if(result.retcode==0){
                    $("#regValidcodeBox").hide();
                    $("#regValidcodeBg").hide();
                    common.init( $("#countBtn"));
                }
                if(result.retcode==111){
                    $("#regValidImg").click();
                    $("#yzm_code").val("");
                    $("#regValidcodeTipText").text(result.msg);
                }
                if(result.retcode==1){
                    $("#regValidcodeBox").hide();
                    $("#regValidcodeBg").hide();
                    $("#error").html(result.msg);
                }
            },
            error:function(er){
                alert(JSON.stringify(er));
            }
        });

    }
    //ajax post数据
    function post_register(phone,code,pass){
        $.ajax({
            url: "/cp/api/user/reg",
            data:{mobile:phone,code:code,password:pass},
            type: "POST",
            dataType:'json',
            success:function(result){
            if(result.retcode==0){
                Toast(result.msg);
                window.location= "http://"+window.location.host;
            }
            if(result.retcode==1){
                Toast(result.msg);
                $("#error").html(result.msg);
            }
        },
        error:function(er){
            alert(JSON.stringify(er));
        }
        });
    }
    //判断手机号是否注册
    function is_mobile_reg(mobile){
        $.ajax({
            url: "/cp/api/user/is_mobile_reg",
            data:{mobile:mobile},
            type: "GET",
            dataType:'json',
            success:function(result){
                //手机号被注册
                if(result.retcode==0){
                    Toast(result.msg);
                    $("#error").html(result.msg);
                    $("#countBtn").attr("disabled","true");
                }else{
                    $("#error").html("");
                    $("#countBtn").removeAttr("disabled");
                }

            },
            error:function(er){
                alert(JSON.stringify(er));
            }
        });
    }
    //text框失去焦点把text框的值赋值给password框
    function textBlur() {
        $("#pass").val($("#text").val());
    }

    //password框失去焦点把password框的值赋值给text框
    function passwordBlur() {
        $("#text").val($("#pass").val());
    }
    if(navigator.userAgent.indexOf("MSIE")>0){
        document.getElementById('phone').attachEvent("onpropertychange",txChange);
    }else if(navigator.userAgent.indexOf("Firefox")>0){
        document.getElementById('phone').addEventListener("input",txChange,false);
    }else{

    }
    function txChange(){
        alert("gaibian");
    }
</script>
</body>
</html>