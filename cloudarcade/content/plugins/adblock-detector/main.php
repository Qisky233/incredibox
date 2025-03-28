<?php
add_to_hook('footer', function(){
	$custom_template = dirname(__FILE__) . '/template-custom.php';
	if (file_exists($custom_template)) {
		include($custom_template);
	} else {
		include(dirname(__FILE__) . '/template-default.php');
	}
	?>
	<script type="text/javascript" src="<?php echo DOMAIN ?>content/plugins/adblock-detector/ad-handler.js"></script>
	<?php
});
?>