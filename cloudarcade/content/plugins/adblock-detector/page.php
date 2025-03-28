<?php

if(isset($_POST['action'])){
	if($_POST['action'] == 'update_html'){
		if(isset($_POST['html']) && $_POST['html'] != ''){
			file_put_contents('../content/plugins/adblock-detector/template-custom.php', $_POST['html']);
			show_alert('Content updated!', 'success');
		} else {
			if(file_exists('../content/plugins/adblock-detector/template-custom.php')){
				unlink('../content/plugins/adblock-detector/template-custom.php');
				show_alert('Content updated!', 'success');
			}
		}
	} else if($_POST['action'] == 'reset_template'){
		if(file_exists('../content/plugins/adblock-detector/template-custom.php')){
			unlink('../content/plugins/adblock-detector/template-custom.php');
			show_alert('Content reset', 'success');
		}
	}
}

$html_template;
$custom_template = ABSPATH.PLUGIN_PATH.'/adblock-detector/template-custom.php';
if(file_exists($custom_template)){
	$html_template = file_get_contents($custom_template);
} else {
	$html_template = file_get_contents(ABSPATH.PLUGIN_PATH.'/adblock-detector/template-default.php');
}

?>
<div class="section">
	<form method="post" enctype="multipart/form-data" action="dashboard.php?viewpage=plugin&name=adblock-detector">
		<input type="hidden" name="action" value="update_html">
		<label class="form-label"><?php _e('HTML Template') ?></label>
		<textarea name="html" class="form-control" rows="12">
<?php echo htmlspecialchars($html_template);?>
		</textarea>
		<div class="mb-3"></div>
		<button class="btn btn-primary"><?php _e('Save') ?></button>
	</form>
	<form method="post" enctype="multipart/form-data" action="dashboard.php?viewpage=plugin&name=adblock-detector">
		<input type="hidden" name="action" value="reset_template">
		<div class="mb-3"></div>
		<button class="btn btn-secondary"><?php _e('Reset') ?></button>
	</form>
	<div class="bs-callout bs-callout-info">
		This plugin only works for themes that implement the 'footer' hook.
	</div>
</div>