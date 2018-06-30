<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title><?php echo strip_tags($msg); ?></title>
    <meta http-equiv="Refresh" content="<?php echo intval($ms/1000) ?>; url=<?php echo $url_forward; ?>">
    <style type="text/css">

        ::selection {
            background-color: #E13300;
            color: white;
        }

        ::moz-selection {
            background-color: #E13300;
            color: white;
        }

        ::webkit-selection {
            background-color: #E13300;
            color: white;
        }

        body {
            background-color: #fff;
            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        #container {
            margin: 10px;
            border: 1px solid #D0D0D0;
            -webkit-box-shadow: 0 0 8px #D0D0D0;
        }

        p {
            margin: 12px 15px 12px 15px;
        }

        #msg {
            padding: 10px;
            font-size: 14px;
        }

        #msg a {
            font-size: 14px;
            text-decoration: none
        }
    </style>
</head>
<body>
<div id="container">
    <h1><?php echo $msg; ?></h1>
    <div id="msg">
        <span><b id="countdown"><?php echo intval($ms / 1000); ?></b>秒后页面自动跳转</span>
        <br/><br/>
        <a href="<?php echo $url_forward; ?>">如果您的浏览器没有跳转请点击这里</a>

    </div>
</div>
<script>
    function go() {
        window.location.href = "<?php echo $url_forward; ?>";
    }
    window.setTimeout(go,<?php echo intval($ms); ?>)
</script>
</body>
</html>
<?php exit; ?>