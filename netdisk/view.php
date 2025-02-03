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
<?php
$weizhi=strrpos($_GET['url'], '.');
$hz=substr($_GET['url'], $weizhi+1);
if($hz == "jpg") {
echo "<img src=" . $_GET['url'] . " />";
} else if($hz == "png") {
echo "<img src=" . $_GET['url'] . " />";
} else if($hz == "jpeg") {
echo "<img src=" . $_GET['url'] . " />";
} else if($hz == "gif") {
echo "<img src=" . $_GET['url'] . " />";
} else if($hz == "mp3") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "ogg") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "wav") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "aac") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "m4a") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "mp4") {
echo "<embed src=" . $_GET['url'] . " />";
} else if($hz == "txt") {
include $_GET['url'];
} else if($hz == "html") {
include $_GET['url'];
} else if($hz == "php") {
include $_GET['url'];
} else if($hz == "css") {
include $_GET['url'];
} else if($hz == "zip") {
$zip = zip_open($_GET['url']);
if ($zip) {
while ($zip_entry = zip_read($zip)) {
echo zip_entry_name($zip_entry) . "<br />";
}
zip_close($zip);
}
}
echo "<list>" . $_GET['name'] . "</list><br>";
echo "<list>" . "<a href=" . $_GET['url'] . ">下载</a>";
?>
</body>
</html>