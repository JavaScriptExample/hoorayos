<?php
	define('ROOT', dirname(__FILE__));
	chdir(ROOT);
	//数据库操作类
	require('inc/medoo.php');
	//函数库
	require('inc/functions.php');
	//配置文件
	require('inc/config.php');
?>