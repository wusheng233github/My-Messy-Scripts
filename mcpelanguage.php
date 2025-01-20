<?php
// 不确定作用
function arrayToProperties($array) {
	$return = "";
	foreach($array as $key => $value) {
		if(!empty($key) && !empty($value)) {
			$return .= "$key=$value\n";
		}
	}
	return $return;
}
function propertiesToArray($properties) {
	$return = [];
	foreach(explode("\n", $properties) as $item) {
		if(empty($item) || substr($item, 0, 1) == "#") {
			continue;
		}
		if(empty($item) || substr($item, strlen($item) - 2, 2) == "	#") {
			$item = substr($item, 0, strlen($item) - 2);
		}
		$explode = explode("=", $item);
		$key = $explode[0];
		$value = $explode[1];
		$return[$key] = $value;
	}
	return $return;
}

$old = propertiesToArray(file_get_contents("en_US.lang"));
$old2 = $old;
$new = propertiesToArray(file_get_contents("zh_CN_119.lang"));
foreach(array_keys($new) as $key) {
	if(isset($old[$key])) {
		if(substr($key, 0, 5) == "item." || substr($key, 0, 5) == "tile.") {
			$old[$key] = substr($old2[$key], 0, strlen($old2[$key]) - 1) . $new[$key];
		} else {
			$old[$key] = $new[$key];
		}
	}
}
$new = propertiesToArray(file_get_contents("zh_CN_011.lang"));
foreach(array_keys($new) as $key) {
	if(isset($old[$key])) {
		if(substr($key, 0, 5) == "item." || substr($key, 0, 5) == "tile.") {
			$old[$key] = substr($old2[$key], 0, strlen($old2[$key]) - 1) . $new[$key];
		} else {
			$old[$key] = $new[$key];
		}
	}
}
$new = propertiesToArray(file_get_contents("zh_CN_014.lang"));
foreach(array_keys($new) as $key) {
	if(isset($old[$key])) {
		if(substr($key, 0, 5) == "item." || substr($key, 0, 5) == "tile.") {
			$old[$key] = substr($old2[$key], 0, strlen($old2[$key]) - 1) . $new[$key];
		} else {
			$old[$key] = $new[$key];
		}
	}
}
file_put_contents("zh_CN.lang", arrayToProperties($old));