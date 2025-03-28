<?php

function plugin_show_admin_info(){
	if(file_exists( dirname( __FILE__ ) . '/content.txt' )){
		echo(file_get_contents(dirname( __FILE__ ) . '/content.txt'));
	}
}

?>