<?php  
	
	require('./api.class.php');
	require('./api.Model.class.php');
	$api = new APi;

	// $dsn = "mysql:host=localhost;dbname=wechat;charset=utf8";
	// $pdo = new PDO($dsn, 'root', 'dasuan9464');
	// // $Content = '大蒜';
	// $sql = "SELECT * FROM infolist";
	// $stm = $pdo->query($sql);
	// echo '<pre>';
	// $res = $stm->fetchAll(2);
	// var_dump($res);
	$timestamp = $_GET['timestamp'];
	$nonce     = $_GET['nonce'];
	$token     = 'xdl2017';
	$signature = $_GET['signature'];
	$echostr   = $_GET['echostr'];
	$array     = array( $timestamp, $nonce, $token);
	sort( $array );
  
	$tmpstr = implode('', $array);
	$tmpstr = sha1( $tmpstr ); 


	if ( $tmpstr == $signature && $echostr) {
		echo $echostr;
		exit;
	} else {
		$api->reponseMsg();
		// var_dump($api);
	}


	// $api->getWxServerIp();
	