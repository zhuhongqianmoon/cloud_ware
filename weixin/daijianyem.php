<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 11:32
 */
echo '<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1,minimum-scale=0.1,user-scalable=0" name="viewport">
		<title></title>
		<style>
			.box1{
				width: 100%;
				height: 50px;
				font: 18px "微软雅黑";
				text-align:center;
			}
		</style>
	</head>
	<body>
		<div class="box1">';
       if($_COOKIE!=null){
           echo $_COOKIE['opendid'];
       }

   echo	  '网站建设中
		</div>
	</body>';

?>