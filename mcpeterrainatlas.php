<?php
// 单个贴图文件夹
$texturedir = "../t";
// 控制转换
$inputfile = "./new.txt";
// terrain.meta，可以各种形状
$metafile = "./terrain.meta";
// 输出的宽高
$imagewidth = 512;
$imageheight = 256;
// 输出的文件
$outputfile = "./output.png";
$floatmode = false;
$oldkey = false; // 兼容一些版本

// 奇怪东西
function decode_properties($properties) {
	$return = [];
	foreach(explode("\n", $properties) as $item) {
		if(empty($item)) {
			continue;
		}
		$explode = explode("=", $item);
		$return[$explode[0]] = $explode[1];
	}
	return $return;
}

$meta = json_decode(file_get_contents($metafile), true);
$table = decode_properties(file_get_contents($inputfile));

$output = imagecreatetruecolor($imagewidth, $imageheight);
// 图片透明好奇怪
imagealphablending($output, false);
imagesavealpha($output, true);
imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
// 屎
foreach($meta as $block) {
	if($oldkey) {
		// 超级屎
		$block["uvs"] = [$block["uv"]];
		foreach($block["additonal_textures"] as $uv) {
			$block["uvs"][] = $uv;
		}
	}
	foreach(array_keys($block["uvs"]) as $uvkey) {
		$texture = imagecreatefrompng($texturedir . "/" . $table[$block["name"] . "+" . $uvkey]);
		if(!isset($table[$block["name"] . "+" . $uvkey])) {
			echo "" . $block["name"] . "+" . $uvkey . "=\n";
		}
		$uv = $block["uvs"][$uvkey];
		if($floatmode) {
			imagecopyresampled($output, $texture, $uv[0] * $uv[4], $uv[1] * $uv[5], 0, 0, round(($uv[2] - $uv[0]) * $uv[4]), round(($uv[3] - $uv[1]) * $uv[5]), imagesx($texture), imagesy($texture));
		} else {
			imagecopyresampled($output, $texture, $uv[0], $uv[1], 0, 0, $uv[2] - $uv[0], $uv[3] - $uv[1], imagesx($texture), imagesy($texture));
		}
	}
}

imagepng($output, $outputfile);