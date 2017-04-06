<?php
	/**
	 *  @todo 查comment, engine等信息
	 *  select * from columns where table_name='表名' and table_schema ='库名';
	 *
	 *	//查看库的详细信息
	 *	select * from tables where table_name='' and table_schema='库名';
	 */
	include 'mysql.config.php';
	include 'template.class.php';
	$dsn = "mysql:host={$db_config['host']};dbname={$db_config['db']}";
	$mysql = new PDO($dsn, $db_config['username'], $db_config['password']);
	$mysql->query("SET NAMES 'UTF8'");


	set_time_limit(0);
	echo $db_config['db'].'库字典生成开始。。。';
	echo '<br/>';
	$path = ROOT_PATH.'/dictionary/';
	if(!file_exists ($path)){
		mkdir($path, 0777, true);
	}
	$filename = $db_config['db'].'.html';
	$file_path = $path.$filename;
	if(file_exists($file_path)){
		unlink($file_path);
	}

	$file = fopen($file_path, 'a+');

	$database_info = [];
	$database_info['name'] = $db_config['db'];
	//生成header, 包括索引
	$sql = "select TABLE_NAME,ENGINE,TABLE_COMMENT from information_schema.tables where  table_schema='{$db_config['db']}'";
	$rs = $mysql->query($sql);
	$tables_info = $rs->fetchAll(PDO::FETCH_ASSOC);

	fwrite($file, head($database_info, $tables_info));

	$sql = "select TABLE_NAME,ENGINE,TABLE_COMMENT from information_schema.tables where  table_schema='{$db_config['db']}'";
	$rs = $mysql->query($sql);

	//生成表字典
	foreach($tables_info as $row){
		$table_name = $row['TABLE_NAME'];
		$table_info = $row;

		$header = ['字段','数据类型','Allow Null','KEY','默认值','EXTRA','备注'];

		$sql = "select COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_KEY,COLUMN_DEFAULT,EXTRA,COLUMN_COMMENT from information_schema.columns where table_name='{$table_name}' and table_schema ='{$db_config['db']}'";
		$rs2 = $mysql->query($sql);
		$columns_info = $rs2->fetchAll(PDO::FETCH_ASSOC);

		$content = container($table_info, $header, $columns_info);

		fwrite($file, $content);
		echo '生成'.$table_name.'完成';
		echo '<br/>';
	}

	fwrite($file, foot());

	echo $db_config['db'].'库字典生成完毕！';
	exit();

	/**
	 * 生成富文本格式
	 * @param [type] $table_info   [description]
	 * @param [type] $header       [description]
	 * @param [type] $columns_info [description]
	 * @param string $format       [description]
	 */
	function container($table_info, $header, $columns_info){
		$tpl = 'container';
		$view = new template(ROOT_PATH);

		$view->assign('header', $header);
		$view->assign('table_info', $table_info);
		$view->assign('columns_info', $columns_info);

		$content = $view->fetch($tpl);
		return $content;
	}

	function head($database_info, $tables_info){
		$tpl = 'head';
		$view = new template(ROOT_PATH);

		$view->assign('database_info', $database_info);
		$view->assign('tables_info', $tables_info);
		$content = $view->fetch($tpl);
		return $content;
	}

	function foot(){
		$tpl = 'foot';
		$view = new template(ROOT_PATH);

		$content = $view->fetch($tpl);
		return $content;
	}

