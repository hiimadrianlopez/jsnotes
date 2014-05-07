<?php


$name = $_POST["name"];
$content = $_POST["content"];
$hidden = @$_POST["hidden"];
$name = ($hidden == true) ? "h-".$name : $name;
//$content = "some text here";

if(!file_exists("../notes/".$name.".txt")){
    $fp = fopen("../notes/".$name.".txt","wb");
	fwrite($fp,stripslashes($content));
	fclose($fp);
}

?>