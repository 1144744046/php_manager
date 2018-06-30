<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/24
 * Time: 18:53
 */

echo "mobile_test_view";

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>欢迎来到萤火虫</title>
    <link type="text/css" rel="stylesheet" href="/static/css/csl_global.css"/>
</head>
<body>
<input type="text" placeholder="请输入常用的手机号" id="u_id">
<input type="button" id="countBtn" value="获取动态码">
<a href="cp/mobile/test/socket">进入聊天</a>
<a href="cp/mobile/test/connect">egineio连接</a>
</body>
<script type="text/javascript" src="/static/js/jquery-3.3.1.min.js"></script>
<script>
    $(document).ready(function() {

        $("#countBtn").click(function(){
            var id=$("#u_id").val()
            post_register(id)
        })

    })
    function post_register(uid){
        $.ajax({
            url: "http://127.0.0.1:5001/user",
            data:{UId:uid},
            type: "POST",
            dataType:'json',
            success:function(result){
                alert(JSON.stringify(result));
            },
            error:function(er){
                alert(JSON.stringify(er));
            }
        });
    }
</script>
</html>
