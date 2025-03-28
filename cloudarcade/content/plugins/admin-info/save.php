<?php

session_start();
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";

if ( $username ) {
	if(isset($_POST['content'])){
		file_put_contents('content.txt', $_POST['content']);
	}
} else {
	die('log out');
}
	
if(isset($_POST['redirect'])){
	header('Location: '.$_POST['redirect']);
}

?>