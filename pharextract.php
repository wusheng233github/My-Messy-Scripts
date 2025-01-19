<?php

$file = "genisys0.14.x.phar";
$folder = "genisys0.14.x";

function recurse_copy($src, $dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ($file = readdir($dir))) {
		if(($file != ".") && ($file != "..")) {
			if(is_dir($src . "/" . $file)) {
				recurse_copy($src . "/" . $file, $dst . "/" . $file);
			} else {
				copy($src . "/" . $file,$dst . "/" . $file);
			}
		}
	}
	closedir($dir);
}
recurse_copy("phar://" . $file, $folder);