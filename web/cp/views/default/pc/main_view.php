<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>欢迎来到萤火虫</title>
    <style type="text/css">
        body{
            padding:0px;
            margin:0px;
        }
        ul li{
            list-style: none;
        }

        .header-top-wrap-v1{
            padding:0px;
            margin:0px;
            width:100%;
            height:46px;
            background-color: #f4f4f4;
            text-align: center;
            color:#888;
            font-size:12px;
        }
        .header-top{
            width:1190px;
            height:46px;
            margin:0 auto;
        }
        .change_city{
            /*width:420px;*/
            float:left;
            height:46px;
            line-height:46px;
            display: inline;

        }
        .change_city a{
            color: #9e9e9e;
            height:46px;
            line-height:46px;
        }
        .change_city a:hover{
            color: #ff6900;
            cursor: pointer;
        }
        .header-top-right {
            float: right;
            height:46px;
            line-height:46px;
        }
        .header-top-user{
            margin:0px;
            height:46px;
            line-height:46px;
        }
        .top-user-login{
            padding-right: 10px;
        }
        .header-top-user li{
            float: left;

            height:46px;
            line-height:46px;
            display: inline;
        }
        .header-top-user a {
            text-decoration: none;
        }
        .login,.register{
            color:#ff6900;
            text-decoration:none ;
            font-size:12px;
        }
        .header-top-warp-v1 .arrow-icon {
            width: 9px;
            height: 6px;
            display: inline-block;
            margin-left: 6px;
            background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAGCAYAAAARx7TFAAABS2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIi8+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgo8P3hwYWNrZXQgZW5kPSJyIj8+IEmuOgAAAE9JREFUCJl1jkENgEAMBAekYOEsYOG01MKuBMxhpby4kKY06Wd2OymSjszkbyWNHRi2L5qxHcB821NSFMNiLaxHW2Z+9RM4gTsivILu0coeUOZsxClrHD4AAAAASUVORK5CYII=') no-repeat center center
        }
        .header-top-right .box-pannel {
            position: absolute;
            left: 0px;
            top: 0px;
            min-width:120px;
            border:1px solid #eee;
            background: #fff;
            line-height:normal;
        }
        .header-top-right .nav-menu-list{
            z-index:10001;
            position:relative;
            margin-top:-1px ;
            display: none;
        }

        .header-top-right .nav-menu-list a {
            display: block;
            line-height: 28px;
            white-space: nowrap;
            color: #6c6c6c;
            text-align: left;
        }
        .header-top-right .nav-menu-list a:hover {
            color: #ff6900;
        }
        .nav a{
            color:#888;
            display: block;
            padding-right: 10px;
            padding-left: 10px;
        }

        .hov{
            background-color: #fff;
        }
        .csl_city_container {
            width: 350px;
            border: 1px solid #eee;
            line-height: normal;
            position: absolute;
            background: transparent;
            z-index: 20000;
            margin: 0px;
        }
        .city_data_title{
            font-weight: 700;
            padding-left: 10px;
            padding-top: 10px;
            padding-bottom: 10px;
            margin: 0px;
            font-size: 12px;
            cursor: pointer;
        }
        .city_line{
            border-bottom: 1px solid #ff6900;
            position: absolute;
            width: 95%;
            left: 10px;
            text-align: center;

        }
        .csl_city_container a{
            width: 70px;
            height: 25px;
            display: inline-block;
            color: #666;
            line-height: 25px;
            text-align: center;
            cursor: pointer;
            font-size: 12px;
            overflow: hidden;
        }

        .csl_city_container a:hover { color: #ff6900; }
    </style>

</head>
<body>
<!-- 代码 开始 -->
<div class="header-top-wrap-v1">
    <div class="header-top">
        <div class="change_city">
            <span id="city"></span>
            <a id="change_city" >[切换城市]</a>
        </div>
        <div class="header-top-right">
            <ul class="header-top-user">
                <li class="top-user-login">
                    <div>
                        <span>请</span>
                        <a class="login" href="cp/pc/login">登录</a>/<a class="register" href="cp/pc/user_center/register">免费注册</a>
                    </div>
                </li>
                <li>|</li>
                <li class="nav">
                    <a href="">我是学生<i class="arrow-icon"></i>
                    </a>
                    <div class="nav-menu-list">
                        <div class="box-pannel">
                            <a href="" class="a-link">我的</a>
                            <a href="" class="a-link">我的</a>
                            <a href="" class="a-link">我的</a>
                        </div>
                    </div>
                </li>
                <li class="nav">
                    <a href="">我是学生<i class="arrow-icon"></i>
                    </a>
                    <div class="nav-menu-list">
                        <div class="box-pannel">
                            <a href="" class="a-link">我的</a>
                            <a href="" class="a-link">我的</a>
                            <a href="" class="a-link">我的</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<script type="text/javascript" src="/static/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="/static/js/csl_city.js"></script>
<script type="text/javascript" src="/static/js/csl_save_data.js"></script>
<!--<script type="text/javascript" src="/static/js/tongji.js"></script>-->
<script type="text/javascript">
    $(document).ready(function() {
        $('.nav').hover(function() {
            $('.nav-menu-list',this).slideDown(200);
            $(this).children('a:first').addClass("hov");
        }, function() {
            $('.nav-menu-list',this).hide();
            $(this).children('a:first').removeClass("hov");
        });

    });
    $(document).bind('click', function(e) {
        var e = e || window.event; //浏览器兼容性
        var elem = e.target || e.srcElement;
        while (elem) { //循环判断至跟节点，防止点击的是div子元素
            if (elem.id && elem.id == City.id) {
                return;
            }
            if(elem.id && elem.id == 'change_city'){

                if(City.is_show){
                    City.show_city($("#city"));
                }else{
                    City.close_city();
                }
                City.is_show=!City.is_show;
                return;
            }
            elem = elem.parentNode;
        }
        if($("#"+City.id).length>0){
            City.is_show=!City.is_show;
            City.close_city();
        }

    });



</script>
<!-- 代码 结束 -->
</body>
</html>