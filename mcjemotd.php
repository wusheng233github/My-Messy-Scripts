<?php
error_reporting(E_ALL | E_STRICT);

$ip = "127.0.0.1";
$port = 25565;
$protocolVersion = "47";
$rawjson = '{"version":{"name":"wusheng233 1.8.9","protocol":47},"players":{"max":100,"online":0},"description":{"text":"Hello world"}}';

function encode_varint($value) {
	$varint = '';
	for ($i = 0; $i < 5; $i++) {
		$byte = $value & 0x7F;
		$value >>= 7;
		if ($value > 0) {
			$byte |= 0x80;
		}
		$varint .= chr($byte);
		if ($value == 0) {
			break;
		}
	}
return $varint;
}

function slog($string) {
file_put_contents("log.txt", file_get_contents("log.txt") . "\n$string");
}

file_put_contents("log.txt", "");
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $ip, $port);
socket_listen($socket, 4);
slog("服务器已启动在: $ip:$port");

while(true) {
	$client = socket_accept($socket);
	if($client !== false) {
		slog("客户端连接");
		while(true) {
			$clientdata = socket_read($client, 1024, PHP_BINARY_READ);
			if(!empty($clientdata)) {
				slog("客户端: $clientdata");
				$next = substr($clientdata, -1);
				slog("下一步: $next");
				if($next == chr(0x01) || $next == chr(0x01) . chr(0x01) . chr(0x00)) {
					slog("客户端想要获取状态");
					socket_write($client, encode_varint(strlen(chr(0x00) . encode_varint(strlen($rawjson)) . $rawjson)) . chr(0x00) . encode_varint(strlen($rawjson)) . $rawjson);
					slog("发送" . encode_varint(strlen(chr(0x00) . encode_varint(strlen($rawjson)) . $rawjson)) . chr(0x00) . encode_varint(strlen($rawjson)) . $rawjson);
					while(true) {
						$clientpingdata = socket_read($client, 1024, PHP_BINARY_READ);
						if(!empty($clientpingdata)) {
							slog("客户端: $clientpingdata");
							socket_write($client, $clientpingdata);
							slog("重复客户端");
							socket_close($client);
							break 2;
						}
					}
				} else {
					slog("不支持的请求");
					socket_close($client);
					break;
				}
			}
		}
	}
}