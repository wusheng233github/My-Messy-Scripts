<?php
if (rename("files/" . $_COOKIE["name"] . "/" . $_GET["form"], "files/" . $_COOKIE["name"] . "/" . $_GET["to"])){
echo "<h1>文件重命名成功！</h1>";
echo "files/" . $_COOKIE["name"] . "/" . $_GET["form"] . "<br><br>" . "files/" . $_COOKIE["name"] . "/" . $_GET["to"];
header("location:./index.php");
}else{
echo "<h1>文件重命名失败</h1>";
echo "files/" . $_COOKIE["name"] . "/" . $_GET["form"] . "<br><br>" . "files/" . $_COOKIE["name"] . "/" . $_GET["to"];
}
?>
