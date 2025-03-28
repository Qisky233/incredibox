<div class="section">
	<p>
		This is a sample plugin to interact with themes. With this plugin, you can show information for visitors or players.
	</p>
	<p>
		How to show information ?<br><br>
		Put code below to your target page (ex: your_theme/home.php)<br><br>
		<code>&#x3C;?php plugin_show_admin_info() ?&#x3E;</code>
	</p>
	<form action="<?php echo DOMAIN . PLUGIN_PATH . $plugin['dir_name'] . '/save.php' ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="redirect" value="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=<?php echo $plugin['dir_name'] ?>">
		<div class="form-group">
			<?php
				$content = '';
				if(file_exists($plugin['path'].'/content.txt')){
					$content = file_get_contents($plugin['path'].'/content.txt');
				}
			?>
			<label>Content (HTML allowed):</label>
			<textarea class="form-control" name="content" rows="5" required><?php echo $content ?></textarea>
		</div>
		<button type="submit" class="btn btn-primary btn-md">Save</button>
	</form>
</div>