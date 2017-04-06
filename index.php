<?php
	//环境准备
	header('Content-Type:text/html;charset=utf-8;');
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	date_default_timezone_set('Asia/Shanghai');
	define('ROOT_PATH', dirname(__FILE__)); 

	include 'gen_dictionary.php';
	