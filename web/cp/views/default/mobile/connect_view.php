<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>EIO Latency</title>
<!--    <link rel="stylesheet" href="style.css" />-->
</head>
<body>
<h1>EIO Latency <span id="latency"></span></h1>
<h2 id="transport">(connecting)</h2>
<canvas id="chart" height="200"></canvas>
聊聊：<input class="form-control" id="txtUserName" type="text" maxlength="20" style="width: 30%; margin-bottom: 15px" />
<button class="btn btn-default" id="btnSend" style="margin-top:15px">发 送</button>

<script type="text/javascript" src="/static/js/jquery-3.3.1.min.js"></script>
<script src="/static/js/base64.js" charset="utf-8"></script>
<script src="/static/js/engine.io.js" charset="utf-8"></script>
<script>
    //var socket =  new eio.Socket("http://127.0.0.1:9996",null)

    var socket = new eio.Socket("http://127.0.0.1:9995",{transports:["polling", "websocket"]});

    socket.on("open",function(){
        console.log("connection is opened!");
        // alert("connection is opened!")
        sendAuth();
        setInterval(function(){
            socket.ping()
        },15000)

        setInterval(function(){
            var msg = '{"sender":123,"receiver":123,"timestamp":0,"msgid":0,"content":"我们都傻逼,968254"}';
            send(msg)
        }, 1000);
    })


    socket.on("message",function(msg){
        console.log("接收: ",decoder(msg))
    })

    socket.on("close",function() {
        console.log("closed");
    })

    function send(msg) {
        //var test = "你们好";
        var encoderStr = encoder(msg);
        console.log("send : ",msg)
        socket.send(encoderStr)
        //socket.sendText(encoderStr)
    }

    //认证信息
    function sendAuth() {
        var auth='{"authId":"123","authPwd":"ok"}';
        send(auth)
    }

    function encoder(msg) {
        return base64encode(utf16to8(msg));
    }

    function decoder(base64Str) {
        return  utf8to16(base64decode(base64Str));
    }


    $("#btnSend").click(function(){
        alert("hehe")

        var msg="siliang"
        send(msg.length)
        send(msg)
    })
</script>
</body>
</html>
