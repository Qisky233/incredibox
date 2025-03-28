<?php

if(!has_admin_access()){
	die();
}

	if(isset($_POST['action'])){
		if($_POST['action'] == 'update-robots'){
			file_put_contents('../robots.txt', $_POST['content']);
			show_alert('robots.txt saved!', 'success', true);
		} elseif($_POST['action'] == 'reset-robots'){
			$default_content = file_get_contents('../content/plugins/robots-txt/robots.txt');
			$domain = DOMAIN;
			if (substr($domain, -1) === '/') {
				$domain = substr($domain, 0, -1);
			}
			$default_content = str_replace('{{domain}}', $domain, $default_content);
			file_put_contents('../robots.txt', $default_content);
			show_alert('robots.txt updated!', 'success', true);
		}
	}
	$content = '';
	if(file_exists('../robots.txt')){
		$content = file_get_contents('../robots.txt');
	} else {
		$content = file_get_contents('../content/plugins/robots-txt/robots.txt');
		$domain = DOMAIN;
		if (substr($domain, -1) === '/') {
			$domain = substr($domain, 0, -1);
		}
		$content = str_replace('{{domain}}', $domain, $content);
	}
?>
<div class="section">
	<?php if(!file_exists('../sitemap.xml')){ ?>
		<div class="bs-callout bs-callout-warning"><b>sitemap.xml</b> not found! You can generate it with "Sitemap" plugin.</div>
	<?php } ?>
	<?php if(!file_exists('../robots.txt')){ ?>
		<div class="bs-callout bs-callout-warning">There is no <b>robots.txt</b> on root, click "SAVE" to create one.</div>
	<?php } ?>
	<p><a href="https://developers.google.com/search/docs/crawling-indexing/robots/intro" target="_blank">What is robots.txt and what the purpose of it?</a></p>
	<h4>robots.txt</h4>
	<form action="" method="post">
		<input type="hidden" name="action" value="update-robots">
		<div class="form-group">
			<textarea class="form-control" name="content" rows="10" required><?php echo $content ?></textarea>
		</div>
		<div class="mb-3"></div>
		<button type="submit" class="btn btn-primary btn-md"><?php _e('Save') ?></button>
	</form>
	<form action="" method="post">
		<input type="hidden" name="action" value="reset-robots">
		<div class="mb-3"></div>
		<button type="submit" class="btn btn-secondary btn-md"><?php _e('Reset') ?></button>
	</form>
</div>