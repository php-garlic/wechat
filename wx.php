<?php  
	
	require('./api.class.php');
	require('./api.Model.class.php');
	$api = new APi;

	$timestamp = $_GET['timestamp'];
	$nonce     = $_GET['nonce'];
	$token     = 'dasuan';
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
	}

	
