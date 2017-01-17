<?php


$home_addr  = strtr(dirname(__FILE__), "\\", "/");
$imagedata = base64_decode($_POST['imgdata']);
$filename = md5(uniqid(rand(), true));
//path where you want to upload image
$relative_path = preg_replace("`\/[^/]*\.php$`i", "/", $_SERVER['PHP_SELF']);
$file = $home_addr.'/uploads/'.$filename.'.png';
$imageurl  =  "http://" . $_SERVER["HTTP_HOST"].$relative_path."uploads/".$filename.'.png';
file_put_contents($file,$imagedata);
echo $filename.'.png';

?>

