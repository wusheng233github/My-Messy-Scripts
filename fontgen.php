MIT License

Copyright (c) 2026 wusheng233

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

MCPE字体生成器 V2
V1版: https://github.com/wusheng233github/minecraftpe-font-generator

<?php
echo "输入TTF字体路径: ";
$font_file = realpath(trim(fgets(STDIN)));
if($font_file === false || !is_file($font_file)) {
    echo "无法加载字体\n";
    exit;
}
echo "输出目录: ";
$output = rtrim(trim(fgets(STDIN)), "/\\") . "/";
$size = 16;
$image_width = $size * 16;
$image_height = $size * 16;
@mkdir($output);
$start = microtime(true);
for($b1 = 0;$b1 < 256;$b1++) {
    if($b1 >= 216 && $b1 < 249) {
        continue;
    }
    $image = imagecreatetruecolor($image_width, $image_height);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
    for($b2 = 0;$b2 < 256;$b2++) {
        $character = imagecreatetruecolor($size, $size);
        $white = imagecolorallocatealpha($character, 255, 255, 255, 0);
        imagefill($character, 0, 0, imagecolorallocatealpha($character, 0, 0, 0, 127));
        imagettftext($character, floor($size * 0.75), 0, 0, $size - floor($size / 8), $white, $font_file, mb_chr(($b1 << 8) | $b2, "UTF-8"));
        imagecopy($image, $character, ($b2 % 16) * $size, floor($b2 / 16) * $size, 0, 0, $size, $size);
    }
    $filename = "glyph_" . str_pad(strtoupper(dechex($b1)), 2, "0", STR_PAD_LEFT) . ".png";
    imagepng($image, $output . $filename);
    imagedestroy($image);
    echo "已生成 $filename \n";
}
echo "用时" . microtime(true) - $start . "\n";
