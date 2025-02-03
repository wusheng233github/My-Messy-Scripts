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
<form action="register.php" method="get" class="mdui-card">
<p class="title">注册</p>
名称: <input type="text" name="name"><br>
密码: <input type="text" name="password"><br>
<img src="./captcha/index.php">
<br>
验证码: <input type="text" name="code">
<input type="submit" class="button">
</form>
<a href="./login.php">登录账号</a>
</body>
</html>
<?php
$code=fopen("./captcha/code.txt","r");
if($_GET["code"] == fread($code,filesize("./captcha/code.txt"))) {
$register=fopen("user/" . $_GET["name"] . ".txt","x") or die("您未填写完整或账号已存在");
fwrite($register, $_GET["password"]) or die("您未填写完整或账号已存在");
mkdir('./files/' . $_GET["name"]);
echo "已注册 " . $_GET["name"] . " 这个账户，";
echo "密码为 " . $_GET["password"] . " ，请牢记";
fclose($register);
} else {
echo "验证码错误";
}
fclose($code);
?>