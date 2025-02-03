<?php
header('Content-type: image/jpeg');
$img=imagecreateTruecolor(200,60) or dir('创建图片失败！');
$colorBack=imagecolorallocate($img,rand(0, 255),rand(0, 255),rand(0, 255));
$color1=imagecolorallocate($img,255,255,255);
$color2=imagecolorallocate($img,rand(0, 255),rand(0, 255),rand(0, 255));
$color3=imagecolorallocate($img,rand(0, 255),rand(0, 255),rand(0, 255));
$color4=imagecolorallocate($img,rand(0, 255),rand(0, 255),rand(0, 255));
$color5=imagecolorallocate($img,rand(0, 255),rand(0, 255),rand(0, 255));
$sj=rand(1000, 9999);
imagefill($img,1,1,$colorBack);
imagestring($img,5,10,10,$sj,$color1);
imagearc($img,rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),$color5);
imagearc($img,rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),$color5);
imagearc($img,rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),$color5);
imagerectangle($img,rand(0, 45),rand(0, 45),rand(0, 45),rand(0, 45),$color2);
//增加干扰点
for($i=0;$i<100;$i++){
$pointcolor=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
imagesetpixel($img,rand(1,99),rand(1,29),$pointcolor);
}
//增加干扰线
for($i=0;$i<5;$i++){
$linecolor=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
imageline($img,rand(1,99),rand(1,29),rand(1,99),rand(1,29),$linecolor);
}
imagepng($img);
imagedestroy($img);
$code=fopen("code.txt","w");
fwrite($code,$sj);
fclose($code);
?>