<?php
unlink('./files/' . $_COOKIE["name"] . "/" . $_GET["file"]);
echo "<h1>删除成功</h1>";
header("location:./index.php");
?>