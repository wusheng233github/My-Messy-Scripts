<?php
// 不成功，游戏崩溃
$img = imagecreatefrompng("./output.png");
$output = "precision mediump float;\n\n// ...\n\nvec2 p=floor(vec2(uv0.x*512.,uv0.y*256.));vec4 d=vec4(0.);";
$o = false;
for($y = 0;$y < imagesy($img);$y++) {
	for($x = 0;$x < imagesx($img);$x++) {
		$rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));
		if(!$o) {
			$o = true;
		} else {
			$output .= "else ";
		}
		$r = $rgb["red"] / 255;
		if(is_int($r)) $r .= ".";
		$g = $rgb["green"] / 255;
		if(is_int($g)) $g .= ".";
		$b = $rgb["blue"] / 255;
		if(is_int($b)) $b .= ".";
		$a = 1 - $rgb["alpha"] / 127;
		if(is_int($a)) $a .= ".";
		if($r == $g && $g == $b && $b == $a) {
			$output .= "if(p.x==" . $x . ".&&p.y==" . $y . ".)d=vec4(" . $r . ");";
		} else {
			$output .= "if(p.x==" . $x . ".&&p.y==" . $y . ".)d=vec4(" . $r . "," . $g . "," . $b . "," . $a . ");";
		}
	}
}
file_put_contents("gl.txt", $output . "diffuse = d;\n");