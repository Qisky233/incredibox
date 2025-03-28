<?php

$url_params = isset($_GET['viewpage']) ? explode("/", $_GET['viewpage']) : [];
$path = '';

if($url_params){
	foreach ($url_params as $key) {
		if($key != '' && $key !== ' '){
			$path .= '/'.$key;
		}
	}
	$path .= '/';
} else {
	$path = '/';
}

$html_file = 'static'.$path.'index.html';
if(file_exists($html_file)){
	if(file_exists(dirname(__FILE__).'/content/plugins/static-site/debug.txt')){ // Debug
		echo '<div style="background: red; color: white; position: fixed; z-index: 10000;">THIS PAGE IS STATIC</div>';
	}
	require_once($html_file);
} else {
	define('NO_STATIC', true);
	require(dirname(__FILE__).'/index.php');
}

?>