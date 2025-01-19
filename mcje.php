<?php
// 粑粑，太垃圾了
stream_set_blocking(STDIN, false);
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("memory_limit", "32M");

// 我没做注释
function propertiesToArray($properties) {
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

function encode_varint($value) {
	$varint = "";
	for($i = 0;$i < 5;++$i) {
		$byte = $value & 0x7F;
		$value >>= 7;
		if($value > 0) {
			$byte |= 0x80;
		}
		$varint .= chr($byte);
		if($value == 0) {
			break;
		}
	}
	return $varint;
}

function decode_varint($varint) {
	$result = 0;
	$offset = 0;
	$size = strlen($varint);
	if($size > 5) {
		$size = 5;
	}
	for($i = 0;$i < $size;++$i) {
		$byte = ord($varint[$i]);
		if(($byte & 0x80) == 0x80) {
			$result |= ($byte & 0x7F) << $offset;
		} else {
			$result |= $byte << $offset;
			break;
		}
		$offset += 7;
	}
	return $result;
}

function encode_unsignedshort($n) {
	return pack("n", $n);
}

$GLOBALS["pool"] = "";

// 粑粑不支持压缩
function mc_unpack($data, $compressed) {
	$data = $GLOBALS["pool"] . $data;
	$array = [];
	while(true) {
		if(strlen($data) <= 0) {
			break;
		}
		$varint = substr($data, 0, 5);
		$num = decode_varint($varint);
		$varintlen = strlen(encode_varint($num));
		$num += $varintlen;
		$pk = substr($data, 0, $num);
		if(strlen($pk) < $num) {
			$GLOBALS["pool"] = $pk;
		} else {
			$GLOBALS["pool"] = "";
			$array[] = $pk;
		}
		$data = substr($data, $num);
	}
	return $array;
}

// 这三个太低效了
function getpacketid($data, $compressed = 0) {
	return hexdec(bin2hex($data[strlen(encode_varint(decode_varint(substr($data, 0, 5)))) + $compressed]));
}

function getpacketdata($data, $compressed = 0) {
	return substr($data, strlen(encode_varint(decode_varint(substr($data, 0, 5)))) + $compressed + 1);
}

function getstring($data) {
	$length = decode_varint(substr($data, 0, 5));
	return substr($data, strlen(encode_varint($length)), $length);
}

// 感觉不太全
function jsontexttostring($json) {
	$string = "";
	$array = json_decode($json, true);
	if($array == null) {
		$string = $json;
	} else if(isset($array["translate"])) {
		$string = translatetostring($array);
	} else if(isset($array["extra"])) {
		$string = extratostring($array["extra"]);
	} else if(isset($array["text"])) {
		$string = $array["text"];
	}
	return $string;
}

function translatetostring($array) {
	$string = "";
	$with = [];
	if(isset($array["with"])) {
		foreach($array["with"] as $item) {
			if(is_string($item)) {
				$with[] = $item;
			} else if(isset($item["translate"])) {
				$with[] = translatetostring($item);
			} else if(isset($item["extra"]) && isset($item["text"])) {
				$with[] = $item["text"] . extratostring($item["extra"]);
			} else if(isset($item["extra"])) {
				$with[] = extratostring($item["extra"]);
			} else if(isset($item["text"])) {
				$with[] = $item["text"];
			}
		}
	}
	if(count($with) == 0) {
		$string = $GLOBALS["translate"][$array["translate"]];
	} else {
		$string = sprintf($GLOBALS["translate"][$array["translate"]], ...$with);
	}
	return $string;
}

function extratostring($array) {
	$string = "";
	foreach($array as $text) {
		if(is_string($text)) {
			$string .= $text;
		} else if(isset($text["translate"])) {
			$string .= translatetostring($text);
		} else if(isset($text["extra"]) && isset($text["text"])) {
			$string .= $text["text"] . extratostring($text["extra"]);
		} else if(isset($text["extra"])) {
			$string .= extratostring($text["extra"]);
		} else if(isset($text["text"])) {
			$string .= $text["text"];
		}
	}
	return $string;
}

function encode_keep_alive($pk, $compressed) {
	return substr_replace($pk, chr(0x0B), 1 + $compressed, 1);
}

function encode_chat_message($input, $compressed) {
	$input = substr($input, 0, 256);
	$chat_message = chr(0x02) . encode_varint(strlen($input)) . $input;
	if($compressed == 1) {
		$chat_message = chr(0) . $chat_message;
	}
	return encode_varint(strlen($chat_message)) . $chat_message;
}

function encode_perform_respawn($compressed) {
	// 0x03(play): client status  0: perform respawn
	$pk = encode_varint(0x03) . encode_varint(0);
	if($compressed == 1) {
		$pk = chr(0) . $pk;
	}
	return encode_varint(strlen($pk)) . $pk;
}

function decode_float($data) {
	$int = 0;
	for($i = 0;$i < 4;$i++) {
		$int |= ord($data[$i]) << ((3 - $i) * 8);
	}
	$sign = ($int >> 31) & 0x01;
	$exponent = ($int >> 23) & 0xFF;
	$mantissa = $int & 0x7FFFFF;
	$signmultiplier = ($sign === 0) ? 1 : -1;
	$trueexponent = $exponent - 127;
	if($exponent === 0) {
		$mantissavalue = $mantissa / (1 << 23);
		$floatvalue = $signmultiplier * (2 ** (-126)) * $mantissavalue;
	} else {
		$mantissavalue = 1 + ($mantissa / (1 << 23));
		$floatvalue = $signmultiplier * (2 ** $trueexponent) * $mantissavalue;
	}
	return $floatvalue;
}

function decode_long($bytes) {
	$result = 0;
	for($i = 0;$i < 8;$i++) {
		$bytevalue = ord($bytes[$i]);
		$result |= ($bytevalue << (8 * (7 - $i)));
	}
	if($result & (1 << 63)) {
		$result -= (1 << 64);
	}
	return $result;
}

function decode_double($binary) {
	$hi = (ord($binary[0]) << 24) | (ord($binary[1]) << 16) | (ord($binary[2]) << 8) | ord($binary[3]);
	$lo = (ord($binary[4]) << 24) | (ord($binary[5]) << 16) | (ord($binary[6]) << 8) | ord($binary[7]);
	$sign = ($hi >> 31) == 0 ? 1 : -1;
	$exponent = (($hi >> 20) & 0x7FF);
	$mantissa = ($hi & 0xFFFFF) * 4294967296.0 + $lo;
	if($exponent == 0) {
		$exponent = 1 - 1023;
	} else {
		$mantissa += 4503599627370496.0;
		$exponent -= 1023;
	}
	return $sign * $mantissa * pow(2, $exponent - 52);
}

// 看着办
$ip = "192.168.134.18";
$port = 25565;
$playername = "wushengtest";

// 这个语言我就不放里面了记得改一下
$GLOBALS["translate"] = propertiesToArray(file_get_contents("./zh_cn.lang"));
// 音效不全
$sounds = explode("\n", file_get_contents("./sounds.txt"));

echo "正在连接 $ip:$port\n";

// 创建套接字并连接
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$connect = socket_connect($server, $ip, $port);
if(!$connect) {
	die("无法连接服务器");
}
// socket_set_nonblock($server);

echo "即将登录 $playername 并开始\n";

// 这两个太丑了
// 0x00(handshaking): handshake  340: 1.12.2  2: login
$handshake = encode_varint(0x00) . encode_varint(340) . encode_varint(strlen($ip)) . $ip . encode_unsignedshort($port) . encode_varint(2);
$handshake = encode_varint(strlen($handshake)) . $handshake;

// 0x00(login): login start
$loginstart = encode_varint(0x00) . encode_varint(strlen($playername)) . $playername;
$loginstart = encode_varint(strlen($loginstart)) . $loginstart;

socket_write($server, $handshake);
socket_write($server, $loginstart);

// 这个$compressed改不动了
$compressed = 0;
$state = 1;
$timer = time();
$health = 1;
$tips = 0;

while(true) {
	$read = socket_read($server, 8192, PHP_BINARY_READ);
	foreach(mc_unpack($read, $compressed) as $packet) {
		// 这个形看着很丑
		if(getpacketid($packet, $compressed) == 0x03 && $state == 1) {
			$varint = getpacketdata($packet, $compressed);
			// -1我编码不出来
			if($varint == "\xff\xff\xff\xff\x0f") {
				echo "\n禁用压缩\n\n";
				$compressed = 0;
				continue;
			}
			echo "\n启用压缩\n\n";
			$compressed = 1;
		} else if(getpacketid($packet, $compressed) == 0x1F && $state == 2) {
			echo "提示: keep alive\n";
			$timer = time();
			socket_write($server, encode_keep_alive($packet, $compressed));
		} else if(getpacketid($packet, $compressed) == 0x0F && $state == 2) {
			echo jsontexttostring(getstring(getpacketdata($packet, $compressed))) . "\n";
		} else if(getpacketid($packet, $compressed) == 0x02 && $state == 1) {
			$state = 2;
		} else if(getpacketid($packet, $compressed) == 0x1A && $state == 2) {
			die(jsontexttostring(getstring(getpacketdata($packet, $compressed))) . "\n");
		} else if(getpacketid($packet, $compressed) == 0x00 && $state == 1) {
			die(jsontexttostring(getstring(getpacketdata($packet, $compressed))) . "\n");
		} else if(getpacketid($packet, $compressed) == 0x01 && $state == 1) {
			die("服务器启用了正版验证\n");
		} else if(getpacketid($packet, $compressed) == 0x41 && $state == 2) {
			$float = decode_float(substr(getpacketdata($packet, $compressed), 0, 4));
			if($float !== $health) {
				echo "生命值变化: $float\n";
				$health = $float;
				if($health <= 0) {
					echo "自动重生\n";
					socket_write($server, encode_perform_respawn($compressed));
				}
			}
		} else if(getpacketid($packet, $compressed) == 0x47 && $state == 2) {
			$time = decode_long(substr(getpacketdata($packet, $compressed), 8, 8));
			while(true) {
				if($time - 24000 < 0) {
					break;
				}
				$time -= 24000;
			}
			if($time >= 23031) {
				if($tips !== 1) {
					echo "怪物停止刷新时间\n";
					$tips = 1;
				}
			} else if($time >= 22200) {
				if($tips !== 2) {
					echo "日出开始\n";
					$tips = 2;
				}
			} else if($time >= 18000) {
				if($tips !== 3) {
					echo "午夜了\n";
					$tips = 3;
				}
			} else if($time >= 13702) {
				if($tips !== 4) {
					echo "太阳落山了\n";
					$tips = 4;
				}
			} else if($time >= 12769) {
				if($tips !== 5) {
					echo "怪物即将刷新时间\n";
					$tips = 5;
				}
			} else if($time >= 12544) {
				if($tips !== 6) {
					echo "黑天了\n";
					$tips = 6;
				}
			} else if($time >= 6000) {
				if($tips !== 7) {
					echo "中午了\n";
					$tips = 7;
				}
			} else if($time >= 450) {
				if($tips !== 8) {
					echo "日出结束\n";
					$tips = 8;
				}
			}
		} else if(getpacketid($packet, $compressed) == 0x2F && $state == 2) {
			$data = getpacketdata($packet, $compressed);
			$x = decode_double(substr($data, 0, 8));
			$y = decode_double(substr($data, 8, 8));
			$z = decode_double(substr($data, 16, 8));
			$yaw = decode_float(substr($data, 24, 4));
			$pitch = decode_float(substr($data, 28, 4));
			echo "位置: $x $y $z\nyaw: $yaw pitch: $pitch\n";
		} else if(getpacketid($packet, $compressed) == 0x34 && $state == 2) {
			// 没试过
			$data = getpacketdata($packet, $compressed);
			$length = decode_varint(substr($data, 0, 5));
			$varintlen = strlen(encode_varint($length));
			$url = substr($data, $varintlen, $length);
			$sha1 = getstring(substr($data, $length + $varintlen));
			echo "服务器资源包: $url\n资源包sha1: $sha1\n";
		} else if(getpacketid($packet, $compressed) == 0x49 && $state == 2) {
			$msg = $sounds[decode_varint(substr(getpacketdata($packet, $compressed), 0, 5))];
			if(isset($GLOBALS["translate"][$msg])) {
				$msg = $GLOBALS["translate"][$msg];
			}
			echo "(" . $msg . ")\n";
		}
	}
	if(time() >= $timer + 30) {
		die("你好像早就掉线了\n");
	}
	// windows上用不了记得去掉
	$message = trim(fgets(STDIN), "\n");
	if(!empty($message)) {
		socket_write($server, encode_chat_message($message, $compressed));
		$message = "";
	}
	$read = "";
}