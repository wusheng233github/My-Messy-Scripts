MIT License

Copyright (c) 2025 wusheng233

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
<?php
function unpackI($data) {
    if(strlen($data) >= 4) {
        $data = unpack("I", $data);
        if(isset($data[1])) {
            return $data[1];
        }
    } else {
        throw new \Exception('unpackI输入长度不够4字节: ' . bin2hex($data));
    }
}
//use ZMusicBox\NoteBoxAPI;
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
set_error_handler(function() {
    exit(1);
}, E_NOTICE);
/*echo '加载依赖，请输入NoteBoxAPI.php路径，不要用wusheng233github/ZMusicBox: ';
include trim(fgets(STDIN)); // !*/
echo '新NBS检测' . PHP_EOL . '请输入songs目录位置如./plugins/songs结尾不要/ >';
foreach(glob(trim(fgets(STDIN)) . "/*.nbs") as $filepath) {
    try {
        $message = "正在检测$filepath\e[22m";
        $data = file_get_contents($filepath);
        $newnbs = false;
        if(substr($data, 0, 2) === "\0\0") {
            $newnbs = true;
            $version = ord(substr($data, 2, 1));
            $message .= " \e[1m\e[33m这个是新NBS V$version\e[22m";
        } else {
            $message = "\e[2m$message";
            //new NoteBoxAPI(__FILE__, $filepath);
        }
        echo $message;
        $offset = 2 + ($newnbs ? 1 + 1 + 2 : 0) + 2;
        $offset += unpackI(substr($data, $offset, 4));
        $offset += 4;
        $offset += unpackI(substr($data, $offset, 4));
        $offset += 4;
        $offset += unpackI(substr($data, $offset, 4));
        $offset += 4;
        $offset += unpackI(substr($data, $offset, 4));
        $offset += 4;
        $offset += 2 + 1 + 1 + 1 + 4 + 4 + 4 + 4 + 4;
        $midi = substr($data, $offset + 4, unpack("I", substr($data, $offset, 4))[1]);
        echo "\e[0m", empty($midi) ? "" : " 这个从midi转换 原文件: $midi";
    } catch(\Exception $e) {
        echo "\e[91m 错误: ", $e->getMessage(), "\e[0m";
    }
    echo PHP_EOL;
}