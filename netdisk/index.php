<html>
<head>
<link rel="stylesheet" href="https://unpkg.com/mdui@1.0.2/dist/css/mdui.min.css" />
<script src="https://unpkg.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
<title>wusheng233网盘</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="./style.css" rel="stylesheet">
<link href="./favicon.ico" rel="icon shortcut" type="image/x-icon">
<link href="https://www.w3school.com.cn/lib/bs/bootstrap.css" rel="stylesheet">
<script src="https://www.w3school.com.cn/lib/bs/bootstrap.js"></script>
<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
</head>
<body class="mdui-ripple mdui-theme-accent-pink mdui-theme-primary-indigo">
<div class="topbar">
<br>
<p>wusheng233网盘</p>
</div>
<button type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop" class="mdui-btn"><i class="fa fa-bars" style="font-size:36px"></i></button>

<form action="index.php" method="post"
enctype="multipart/form-data">
<label for="file"></label>
<input type="file" name="file" id="file" class="mdui-fab-fixed" /> 
<br />
<button type="submit" name="submit" value="上传" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"/><i class="mdui-icon material-icons">&#xe2c6;</i></button>
</form>
<p>直链:</p>
<p>wusheng233.ksweb.club/cloud/files/用户名/文件名</p>
<?php
//输出所有文件
$folders = opendir('./files/' . $_COOKIE["name"] . "/");
while($f = readdir($folders)){
    echo "<list>" . $f . " 预览链接: " . "<a href=" . "./view.php?name=" . $f . "&url=" . "./files/" . $_COOKIE["name"] . "/" . $f . ">点我</a></list><br>";
}
closedir($folders);
//上传文件
  if ($_FILES["file"]["error"] > 0)
    {
    }
  else
    {
    if (file_exists("files/" . $_COOKIE["name"] . "/" . $_FILES["file"]["name"]))
      {
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "files/" . $_COOKIE["name"] . "/" . $_FILES["file"]["name"]);
      }
    }
//检测是否登录
if ($_COOKIE["loginlock"] == "loginlock") {
    if ($_COOKIE["name"] == "") {
        echo "你没有填写用户名";
        header("location:./login.php");
    } else {
        if ($_COOKIE["password"] == "") {
            echo "你没有填写密码";
            header("location:./login.php");
        } else {
            $login = fopen("user/" . $_COOKIE["name"] . ".txt", "r");
            $password = fread($login, filesize("user/" . $_COOKIE["name"] . ".txt"));
            if ($_COOKIE["password"] == $password) {
                echo "登录成功";
            } else {
                echo "密码或用户名错误";
                echo $_COOKIE["password"];
                echo $_COOKIE["name"];
                header("location:./login.php");
            }
        }
    }
} else {
echo "错误";
header("location:./login.php");
}
?>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
  <div class="offcanvas-header topbar">
    <p id="offcanvasTopLabel">工具</p>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
  <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 900'><rect fill='#05348B' width='1600' height='900'/><polygon fill='#3a5ea2'  points='957 450 539 900 1396 900'/><polygon fill='#042461'  points='957 450 872.9 900 1396 900'/><polygon fill='#875da8'  points='-60 900 398 662 816 900'/><polygon fill='#5f2c70'  points='337 900 398 662 816 900'/><polygon fill='#c35f9e'  points='1203 546 1552 900 876 900'/><polygon fill='#9f3b74'  points='1203 546 1552 900 1162 900'/><polygon fill='#ec6a85'  points='641 695 886 900 367 900'/><polygon fill='#cf5169'  points='587 900 641 695 886 900'/><polygon fill='#fe8062'  points='1710 900 1401 632 1096 900'/><polygon fill='#ee7859'  points='1710 900 1401 632 1365 900'/><polygon fill='#f9a648'  points='1210 900 971 687 725 900'/><polygon fill='#f9a647'  points='943 900 1210 900 971 687'/></svg>
<?php
include './delete.html';
include './rename.html';
?>
</div>
</div>
<script>
mdui.snackbar({
  message: '一定要上传文件，禁止上传垃圾，禁止攻击'
});
</script>
</body>
</html>