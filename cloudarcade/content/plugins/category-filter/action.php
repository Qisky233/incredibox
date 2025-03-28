<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );

if(is_login() && USER_ADMIN && !ADMIN_DEMO){
	if(isset($_POST['action'])){
		if($_POST['action'] == "submit"){
			$json = json_decode($_POST['data'], true);
			update_option("category-filter", json_encode($json));
			echo "ok";
		}
	}
}

?>