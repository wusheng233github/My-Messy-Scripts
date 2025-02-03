<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<title>为什么！MCPE圈子只有这些人？ — <?php if(!empty($_GET["q"])) { echo $_GET["q"]; } else if(!empty($_GET["gq"])) { echo $_GET["gq"]; } else { echo "首页"; } ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=5.0">
		<meta charset="utf-8">
		<link href="https://cdn.bootcdn.net/ajax/libs/bootswatch/5.3.3/flatly/bootstrap.min.css" rel="stylesheet">
		<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.min.js"></script>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php">为啥MCPE圈子只有点人？ - <?php if(!empty($_GET["q"])) { echo $_GET["q"]; } else if(!empty($_GET["gq"])) { echo $_GET["gq"]; } else { echo "首页"; } ?></a>
			</div>
		</nav>
		<?php
			if(!empty($_GET["q"])) {
				$list = json_decode(file_get_contents("players.json"), true);
				$found = false;
				foreach($list as $item) {
					if($item[0] == $_GET["q"]) {
						$found = $item;
					}
				}
				if($found == false) {
					die("<center class=\"mt-5\"><h3>对不起，未找到此玩家😭</h3></center></body></html>");
				}
				echo "<h1 class=\"mx-5 my-4\">玩家名称: {$found[1]}</h1><h2 class=\"mx-5 my-3\">QQ号: {$found[0]}</h2><h1 class=\"mx-3 my-5\">加入的群聊:</h1><ol class=\"list-group\">";
				$list = json_decode(file_get_contents("groups.json"), true);
				foreach($list as $item) {
					if(in_array($found[0], $item[2])) {
						echo "<a href=\"index.php?gq={$item[0]}\"><li class=\"list-group-item d-flex justify-content-between align-items-start\"><div class=\"ms-2 me-auto\"><div class=\"fw-bold\">{$item[0]}</div>{$item[1]}</div></li></a>";
					}
				}
				echo "</ol>";
				echo "<a class=\"mx-2\" href=\"edit.php?q={$_GET["q"]}\">编辑</a>";
				die("</body></html>");
			}
			if(!empty($_GET["gq"])) {
				$list = json_decode(file_get_contents("groups.json"), true);
				$found = false;
				foreach($list as $item) {
					if($item[0] == $_GET["gq"]) {
						$found = $item;
					}
				}
				if($found == false) {
					die("<center class=\"mt-5\"><h3>对不起，未找到此群聊😭</h3></center></body></html>");
				}
				echo "<h1 class=\"mx-5 my-4\">群聊名称: {$found[1]}</h1><h2 class=\"mx-5 my-3\">QQ群号: {$found[0]}</h2><h1 class=\"mx-3 my-5\">成员:</h1><ol class=\"list-group\">";
				foreach($found[2] as $item) {
					$playerlist = json_decode(file_get_contents("players.json"), true);
					foreach($playerlist as $player) {
						if($player[0] == $item) {
							echo "<a href=\"index.php?q={$player[0]}\"><li class=\"list-group-item d-flex justify-content-between align-items-start\"><div class=\"ms-2 me-auto\"><div class=\"fw-bold\">{$player[0]}</div>{$player[1]}</div></li></a>";
						}
					}
				}
				echo "</ol>";
				echo "<a class=\"mx-2\" href=\"edit.php?gq={$_GET["gq"]}\">编辑</a>";
				die("</body></html>");
			}
		?>
		<div class="container">
			<h3 class="my-2">奇了怪了这堆傻逼们怎么这么少</h3>
		</div>
		<h1 class="mx-4">玩家大全</h1>
		<ol class="list-group">
			<?php
				$list = json_decode(file_get_contents("players.json"), true);
				foreach($list as $item) {
					echo "<a href=\"index.php?q={$item[0]}\"><li class=\"list-group-item d-flex justify-content-between align-items-start\"><div class=\"ms-2 me-auto\"><div class=\"fw-bold\">{$item[0]}</div>{$item[1]}</div></li></a>";
				}
			?>
		</ol>
		<h1 class="mx-4">群聊大全</h1>
		<ol class="list-group">
			<?php
				$list = json_decode(file_get_contents("groups.json"), true);
				foreach($list as $item) {
					echo "<a href=\"index.php?q={$item[0]}\"><li class=\"list-group-item d-flex justify-content-between align-items-start\"><div class=\"ms-2 me-auto\"><div class=\"fw-bold\">{$item[0]}</div>{$item[1]}</div></li></a>";
				}
			?>
		</ol>
	</body>
</html>