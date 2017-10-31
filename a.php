<?php
$filename = 'c:\\a.txt';
$handle = fopen($filename, 'rb');

$str = fread($handle, filesize($filename));

echo $str;

echo '<br >';

echo iconv('gbk', 'utf-8', $str);