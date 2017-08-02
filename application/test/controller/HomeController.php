<?php
$url="http://wthrcdn.etouch.cn/weather_mini?city=长春";
$json = file_get_contents("compress.zlib://".$url);
$json = json_decode($json,true);
var_dump($json['data']['city']);
echo '<br/>';
// foreach ($json as $key => $value) {
// 	var_dump($value);
// 	echo '<br/>';
// 	var_dump($key);
// }