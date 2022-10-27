<?php 
	require 'config.php';
	function DB(){
		static $instance;
		if ($instance === NULL){
			$opt = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES => false,
			);
			$dsn = 'mysql:host='.$GLOBALS['host'].';dbname='.$GLOBALS['dbname'].';';
			try {
				$instance = new PDO($dsn, $GLOBALS['user'] ,$GLOBALS['pass'] , $opt);
			} catch (Exception $e) {
				die("Error connect to database");
			}
		}
		return $instance;
	}
	$db = DB();