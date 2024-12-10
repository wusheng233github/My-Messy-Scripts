<?php
// 仅测试在0.14左右
$tgawidth = 512;
$tgaheight = 256;
$blockwidth = 16;
$blockheight = 16;
// 改形状的terrain.meta
$output = "terrain2.meta";
// 原版的terrain.meta
$vanilla = json_decode(file_get_contents("terrain.meta"), true);
$outputarray = [];
$x = 0;
$y = 0;
foreach($vanilla as $block) {
	$uvs = count($block["uvs"]);
	$uvsarray = [];
	for($n = 0;$n < $uvs;++$n) {
		if($x >= $tgawidth) {
			$x = 0;
			$y = $y + $blockheight;
		}
		$uvsarray[] = [$x, $y, $x + $blockwidth, $y + $blockheight, $tgawidth, $tgaheight];
		$x = $x + $blockwidth;
	}
	$outputarray[] = ["name" => $block["name"], "uvs" => $uvsarray];
}
file_put_contents($output, json_encode($outputarray));