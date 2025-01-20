<?php
/*
--------------------
服务器设置
--------------------
*/
// IP
$ip = "0.0.0.0";
// 端口
$port = 25565;
// 最大玩家数量
$maxPlayers = 100;
// MOTD
$MOTD = '{"version":{"name":"wusheng233","protocol":340},"players":{"max":' . $maxPlayers . ',"online":0},"description":{"text":"https:\/\/gitee.com\/wusheng233\/mc"}}';
// 踢玩家的话
$disconnectReason = '{"translate":"你不能连接"}';
// 玩家UUID
$playerUUID = "00000000-0000-0000-0000-000000000000";
// 玩家实体ID
$playerEID = 0;
// 要不要踢了玩家
$kick = false;
// 游戏模式，你知道的，数字
$gameMode = 1;
// 维度，-1: 地狱, 0: 主世界, 1: 末地;
$dimension = 0; 
// 难度，0: 和平, 1: 简单, 2: 普通, 3: 困难
$difficulty = 2;
// 世界类型default(默认), flat(超平坦), largeBiomes(巨型生物群系), amplified(放大化), default_1_1(不知道这是什么)
$levelType = "default";
// 简化调试信息，1为是，0为否
$reducedDebugInfo = 0;
// 玩家名称，不知道怎么弄到
$playerName = "Steve";
// 玩家出生点(X, Y, Z)
$playerSpawnPointX = 0;
$playerSpawnPointY = 0;
$playerSpawnPointZ = 0;
// 玩家面向方向(Yaw, Pitch)
$playerSpawnYaw = 0;
$playerSpawnPitch = 0;
// 玩家传送ID
$playerTeleportID = 0;
// 玩家Ping(毫秒)
$playerPing = 0;



/*
--------------------
常用函数
--------------------
*/
error_reporting(E_ALL | E_STRICT);

// 编码Varint，我不知道这个对不对
function encode_varint($value) {
	$varint = '';
	for($i = 0; $i < 5; $i++) {
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

function decode_varint($value) {
	$pos = 0;
	$result = 0;
	$shift = 0;
	while($pos < strlen($value)) {
		$byte = ord($value[$pos++]);
		$result |= ($byte & 0x7F) << $shift;
		if(($byte & 0x80) === 0) {
			return $result;
		}
		$shift += 7;
		if($shift >= 32) {
			return false;
		}
	}
	return false;
}

// 写入日志
function slog($string) {
	echo "$string \n";
	file_put_contents("log.txt", file_get_contents("log.txt") . "$string\n");
}

// 判断下一步
function getnext($clientData) {
	// 最后一个字符
	$last1char = substr($clientData, -1);
	// 最后三个字符
	$last3char = substr($clientData, -3);
	// 第一行
	$line1 = explode("\n", $clientData)[0];
	// 第一行倒数第三个字符
	$line1last3char = substr($line1, -3, 1);
	// 我不是很熟练使用 || 我怕出问题还有我懒
	// 主要判断
	// 客户端想要获得状态
	if($last1char == "\x01") {
		return 1;
	} else if($last3char == "\x01\x01\x00") {
		return 1;
	// 客户端想要加入游戏(太麻烦了就else了)
	} else {
		return 2;
	}
}

// 不考虑压缩
function mc_pack($id, $data) {
	$return = encode_varint($id) . $data;
	return encode_varint(strlen($return)) . $return;
}

function encode_string($string) {
	return encode_varint(strlen($string)) . $string;
}




/*
--------------------
数据包编码
--------------------
*/
// 编码返回状态
$status = encode_varint(strlen($MOTD)) . $MOTD;
$status = mc_pack(0x00, $status);
// 编码踢玩家
$kickPacket = encode_string($disconnectReason);
$kickPacket = mc_pack(0x00, $kickPacket);
// 编码加入游戏
$joinGamePacket = pack("N", $playerEID) . chr($gameMode) . pack("N", $dimension) . chr($difficulty) . chr($maxPlayers) . chr(strlen($levelType)) . $levelType . chr($reducedDebugInfo);
$joinGamePacket = mc_pack(0x23, $joinGamePacket);
// 编码玩家位置和面向方向
$playerPositionAndLookPacket = pack("d", $playerSpawnPointX) . pack("d", $playerSpawnPointY) . pack("d", $playerSpawnPointZ) . pack("f", $playerSpawnYaw) . pack("f", $playerSpawnPitch) . chr(0x00) . encode_varint($playerTeleportID);
$playerPositionAndLookPacket = mc_pack(0x2F, $playerPositionAndLookPacket);
// 登录成功
$loginSuccessPacket = encode_string($playerUUID) . encode_string($playerName);
$loginSuccessPacket = mc_pack(0x02, $loginSuccessPacket);
// 保持活力
$keepAlivePacket = pack("q", rand(-9223372036854775808, 9223372036854775807));
$keepAlivePacket = mc_pack(0x1F, $keepAlivePacket);
function chatMessagePacket($playerName, $message) {
	return mc_pack(0x0F, encode_string("{\"text\": \"$playerName: $message\"}") . chr(0));
}


/*
--------------------
监听
--------------------
*/
// 清空日志
file_put_contents("log.txt", "");
// 开启
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$bind = socket_bind($socket, $ip, $port);
if(!$bind) {
	slog("端口被占用");
	echo "端口被占用";
	exit;
}
socket_listen($socket, 4);
slog("服务器已启动在: $ip:$port 最大玩家数量: $maxPlayers 世界类型: $levelType");




/*
--------------------
垃圾主要逻辑处理
--------------------
*/
while(true) {
	$client = socket_accept($socket);
	// 如果有连接
	if($client !== false) {
		slog("客户端连接");
		// 等待客户端发送
		while(true) {
			// 接收
			$clientData = socket_read($client, 1024, PHP_BINARY_READ);
			// 如果客户端发送东西了
			if(!empty($clientData)) {
				slog("客户端: $clientData");
				// 判断客户端想要什么 
				$next = getnext($clientData);
				// 如果要获取状态
				if($next == 1) {
					// 给客户端发送编码的数据
					socket_write($client, $status);
					slog("发送: $status");
					// 等待客户端发送下一个
					while(true) {
						// 接收
						$clientPingData = socket_read($client, 1024, PHP_BINARY_READ);
						// 如果客户端发送东西了
						if(!empty($clientPingData)) {
							slog("客户端: $clientPingData");
							// 重复一遍
							socket_write($client, $clientPingData);
							slog("重复客户端");
							// 关闭连接
							socket_close($client);
							// 跳出循环
							break 2;
						}
					}
				// 如果要加入游戏
				} else if($next == 2) {
					if($kick) {
						// 踢了他
						socket_write($client, $kickPacket);
						slog("发送: $kickPacket");
						slog("玩家被踢出: $disconnectReason");
					} else {
						// 按顺序发送
						slog("玩家名称: " . $playerName);
						socket_write($client, $loginSuccessPacket);
						slog("发送: $loginSuccessPacket");
						socket_write($client, $joinGamePacket);
						slog("发送: $joinGamePacket");
						slog("玩家加入游戏: 实体ID: $playerEID 游戏模式: $gameMode 维度: $dimension 简化调试信息: $reducedDebugInfo");
						socket_write($client, $playerPositionAndLookPacket);
						slog("发送: $playerPositionAndLookPacket");
						slog("玩家出生位置: XYZ: $playerSpawnPointX, $playerSpawnPointY, $playerSpawnPointZ");
						$pid = pcntl_fork();
						if($pid) {
							// 聊天信息
							while(true) {
								// 接收
								$clientData = socket_read($client, 1024, PHP_BINARY_READ);
								$say = strpos($clientData, "\x02");
								if($say !== false) {
									$say = substr($clientData, 0, $say);
									if(strlen($say) < 6) {
									}
								}
							}
						} else {
							// 暂时先这样
							while(true) {
								// 别让客户端超时
								$sendKeepAlivePacket = socket_write($client, $keepAlivePacket);
								sleep(20);
							}
						}
					}
					// 关闭连接
					// socket_close($client);
					// 跳出循环
					break;
				}
			}
		}
	}
}