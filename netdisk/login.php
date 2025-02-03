<html>
<head>
<title>wusheng233网盘</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="style.css">
<link href="https://www.w3school.com.cn/lib/bs/bootstrap.css" rel="stylesheet">
<script src="https://www.w3school.com.cn/lib/bs/bootstrap.js"></script>
<link href="./favicon.ico" rel="icon shortcut" type="image/x-icon">
</head>
<body>
<div class="topbar">
<br>
<p>wusheng233网盘</p>
</div>
<form action="login.php" method="get">
<p class="title mdui-card-content">登录</p>
名称: <input type="text" name="name"><br>
密码: <input type="text" name="password"><br>
<input type="submit" class="button">
</form>
<a href="./register.php">注册账号</a>
</body>
</html>
<?php
if ($_COOKIE["loginlock"] == "loginlock") {
    header("location:./index.php");
} else {
    if ($_GET["name"] == "") {
        echo "你没有填写用户名";
    } else {
        if ($_GET["password"] == "") {
            echo "你没有填写密码";
        } else {
            $login = fopen("user/" . $_GET["name"] . ".txt", "r");
            $password = fread($login, filesize("user/" . $_GET["name"] . ".txt"));
            if ($_GET["password"] == $password) {
                echo "登录成功";
                setcookie("name", $_GET["name"], time() + 3600 * 24 * 365);
                setcookie("loginlock", "loginlock", time() + 3600 * 24 * 365);
                setcookie("password", $_GET["password"], time() + 3600 * 24 * 365);
                header("location:./index.php");
            } else {
                echo "密码或用户名错误";
            }
        }
    }
}
?>