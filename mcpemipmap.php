<?php
$input = "output.png";
$levels = 4;

$img = imagecreatefrompng($input);
$width = imagesx($img) / 2;
$height = imagesy($img) / 2;
for($i = 0;$i < 4;++$i) {
    $mipmap = imagecreatetruecolor($width, $height);
    imagealphablending($mipmap, false);
    imagesavealpha($mipmap, true);
    imagefill($mipmap, 0, 0, imagecolorallocatealpha($mipmap, 0, 0, 0, 127));
    imagecopyresized($mipmap, $img, 0, 0, 0, 0, $width, $height, imagesx($img), imagesy($img));
    imagepng($mipmap, "terrain-atlas_mip" . $i . ".png");
    $width = $width / 2;
    $height = $height / 2;
}