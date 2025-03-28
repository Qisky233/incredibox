<?php

$list = get_option('ads-manager');
$array;
if(!$list){
	$array = array(
		'IMA' => array(
			'value' => '',
			'description' => 'Also called Google Ads',
			'placeholder' => 'IMA Ad Tag',
			'default' => true
		),
		'Banner' => array(
			'value' => '', //Deprecated
			'description' => 'Custom banner ads (Image)',
			'placeholder' => 'Image url', //Deprecated
			'url' => '',
			'data' => null,
			'selected' => 'random',
			'default' => false
		)
	);
	update_option('ads-manager', json_encode($array));
} else {
	$array = json_decode($list, true);
}

$tab_list = array(
	'general' => 'General',
	'banners' => 'Banners',
	'stats' => 'Statistics'
);

$slug = isset($_GET['slug']) ? $_GET['slug'] : 'general';

if(isset($_POST['action'])){
	if($_POST['action'] == 'update_tag'){
		foreach ($array as $tag => $item) {
			if($tag == $_POST['Default']){
				$array[$tag]['default'] = true;
			} else {
				$array[$tag]['default'] = false;
			}
			if(isset($_POST[$tag])){
				$array[$tag]['value'] = $_POST[$tag];
				if(isset($array[$tag]['url'])){
					if(isset($_POST[$tag.'-url'])){
						$array[$tag]['url'] = $_POST[$tag.'-url'];
					}
				}
			}
		}
		update_option('ads-manager', json_encode($array));
		show_alert('Tags Updated', 'success');
	} elseif($_POST['action'] == 'update-banner-data'){
		if($_POST['banner-data'] != ''){
			$ok = 1;
			$arr2 = [];
			try {
				$string = $_POST['banner-data'];
				$string = str_replace(['name:','image-url:','banner-url:',' ',"\r\n"], '', $string);
				$arr = explode (",", $string);
				$i = 0;
				$j = 0;
				$cur_data = array();
				foreach ($arr as $item) {
					switch ($i) {
						case 0:
							$cur_data['name'] = esc_slug($item);
							break;
						case 1:
							$cur_data['image'] = esc_url($item);
							break;
						case 2:
							$cur_data['url'] = esc_url($item);
							$arr2[] = $cur_data;
							break;
					}
					$i++;
					if($i > 2){
						$i = 0;
					}
				}
			} catch(Exception $err) {
				var_dump($err);
				$ok = 0;
			}
			if($ok == 1){
				$array['Banner']['data'] = $arr2;
				if(!isset($array['Banner']['selected'])){
					$array['Banner']['selected'] = 'random';
				}
				update_option('ads-manager', json_encode($array));
				show_alert('Banner Data Updated', 'success');
			} else {
				show_alert('Error', 'danger');
			}
		} else {
			$array['Banner']['data'] = null;
			$array['Banner']['selected'] = 'random';
			update_option('ads-manager', json_encode($array));
			show_alert('Banner Data Updated', 'success');
		}
	} elseif($_POST['action'] == 'clear-stats'){
		update_option('ads-manager-stats', '');
	}
}
?>

<div id="action-info"></div>
<div class="section">
	<div class="alert alert-warning" role="alert">
		This plugin is deprecated and no replacement yet!
	</div>
	<div class="bs-callout bs-callout-warning">
		The IMA SDK is not suitable for HTML5 games. Instead, a method called "H5 Game Ads" is recommended. <a href="https://adsense.google.com/start/solutions/h5-games-ads/" target="_blank">Click here for more information and to apply for H5 Ads.</a>
		<br>
		Our previous submission for H5 Ads was not approved, as CloudArcade is a Content Management System (CMS), not a game distributor or a game portal with a substantial number of visitors.
		<br>
		If your site gets approved by Google H5, please let us know. We will be happy to rewrite this plugin and test real in-game ads with your ad tag. It's important for us to ensure the plugin works well with real ads.
</div>
	<ul class="nav nav-tabs">
		<?php
		foreach($tab_list as $tab => $label){
			$active = '';
			if($tab == $slug){
				$active = 'active';
			}
			?>
			<li class="nav-item">
				<a class="nav-link <?php echo $active ?>" href="dashboard.php?viewpage=plugin&name=ads-manager&slug=<?php echo $tab ?>"><?php _e($label) ?></a>
			</li>
			<?php
		}
		?>
	</ul>
	<div class="mb-4"></div>
	<?php if($slug == 'general'){ ?>
		<p>
			Ads Manager plugin is used to manage and show pre-roll or in-game ads, or simply advertisement that loaded and shown inside the game.<br>
			This ad only works with self-uploaded/self-hosted games that integrated with the latest version of CloudArcade API.
		</p>
		<p>
			<a href="https://cloudarcade.net/tutorial/ads-manager-plugin/" target="_blank">Guide: how to use "Ads Manager" plugin</a>
		</p>
		<div class="mb-5"></div>
		<form id="form-ads-manager" method="post" enctype="multipart">
			<input type="hidden" name="action" value="update_tag">
		  <div class="form-group row">
			<label for="select-default" class="col-sm-2 col-form-label">Default Ad</label>
			<div class="col-sm-10">
				<select id="select-default" class="form-control" name="Default">
				<?php
				foreach ($array as $tag => $item) {
					$selected = '';
					if($item['default']){
						$selected = 'selected';
					}
					echo '<option value="'.$tag.'" '.$selected.'>'.$tag.'</option>';
				}
				?>
				</select>
			</div>
		  </div>
		  <hr>
		  <?php
		  foreach ($array as $tag => $item) {
			?>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label"><?php echo $tag ?> <i class="ml-3 fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo $item['description'] ?>"></i></label>
				<div class="col-sm-10">
				  <?php if(isset($item['url'])){ //Is banner ?>
					<div class="mb-2"></div>
					<?php
					if(isset($item['data']) && $item['data']){
						echo '<p>Randomly shown.</p>';
					} else {
						echo '<a href="dashboard.php?viewpage=plugin&name=ads-manager&slug=banners"><div class="btn btn-success btn-sm">Create banner</div></a>';
					}
					?>
				  <?php } else { ?>
					<input type="text" class="form-control" name="<?php echo $tag ?>" placeholder="<?php echo $item['placeholder'] ?>" value="<?php echo $item['value'] ?>">
				  <?php } ?>
				</div>
			  </div>
			<?php
		  }
		  ?>
		  <button type="submit" class="btn btn-primary btn-md">Save</button>
		</form>
	<?php } elseif($slug == 'banners'){
		$banner_data = '';
		if(isset($array['Banner']['data']) && $array['Banner']['data']){
			$banners = $array['Banner']['data'];
			foreach ($banners as $banner) {
				$banner_data .= 'name:'.$banner['name'].',';
				$banner_data .= 'image-url:'.$banner['image'].',';
				$banner_data .= 'banner-url:'.$banner['url'].",\n";
			}
			$banner_data = substr($banner_data, 0, -1);
		}
		?>
		<div class="plugin-banner-creator">
			<form id="form-banner-data" action="#" method="post" enctype="multipart">
				<input type="hidden" name="action" value="update-banner-data">
				<div class="form-group">
					<label for="banner-data">Banner Data:</label>
					<textarea class="form-control" id="banner-data" name="banner-data" rows="4"><?php echo $banner_data ?></textarea>
				</div>
				<button name="submit" id="save-banner" class="btn btn-primary btn-md">Save</button>
			</form>
			<br>
			<form id="form-insert-banner" action="#" method="post">
				<div class="form-group row">
					<div class="col">
						<label>Banner name/key (Unique)</label>
					</div>
					<div class="col">
						<label>Image URL</label>
					</div>
					<div class="col">
						<label>Banner URL</label>
					</div>
				</div>
				<div class="form-group row">
					<div class="col">
						<input type="text" class="form-control b-name" maxlength="15" placeholder="unique name" name="val" value="" required>
					</div>
					<div class="col">
						<input type="text" class="form-control b-image" name="val" placeholder="https://" value="" required>
					</div>
					<div class="col">
						<input type="text" class="form-control b-url" name="val" placeholder="https://" value="" required>
					</div>
				</div>
				<button name="submit" id="insert-row" class="btn btn-success btn-md">Add / Insert</button>
			</form>
		</div>
	<?php } elseif($slug == 'stats'){ ?>
		<p>Banner ad statistics</p>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th><?php _e('Name / Key') ?></th>
						<th><?php _e('Views') ?></th>
						<th><?php _e('Clicks') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$index = 0;
					$ad_stats = get_option('ads-manager-stats');
					if($ad_stats){
						$ad_stats = json_decode($ad_stats, true);
					} else {
						$ad_stats = array();
					}
					foreach ( $ad_stats as $name => $item ) {
						$index++;
						?>
					<tr>
						<th scope="row"><?php echo $index ?></th>
						<td>
							<?php echo '<strong>'.$name.'</strong>' ?>
						</td>
						<td><?php echo $item['views'] ?></td>
						<td><?php echo $item['clicks'] ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<form id="form-ads-manager" method="post" enctype="multipart">
			<input type="hidden" name="action" value="clear-stats">
			<button type="submit" class="btn btn-danger btn-md">Clear Stats</button>
		</form>
	<?php } ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('form#form-ads-manager').submit(function(event){
			//event.preventDefault();
			console.log($(this).serializeArray());
			/*$.ajax({
				url: '/content/plugins/game-reports/action.php',
				type: 'POST',
				dataType: 'json',
				data: {action: 'delete', id: id},
				complete: function (data) {
					console.log(data.responseText);
					if(data.responseText === 'deleted'){
						$('#action-info').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Report deleted!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						$('#tr-'+id).remove();
					} else {
						$('#action-info').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">Failed! Check console log<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					}
				}
			});*/
		});
		$('form#form-insert-banner').submit((event)=>{
			event.preventDefault();
			let b_name = $('.b-name').val();
			let b_image = $('.b-image').val();
			let b_url = $('.b-url').val();
			if(b_name && b_image && b_url){
				b_name = b_name.replace(/[^a-zA-Z0-9]/g, "").toLowerCase();
				$('#banner-data').append('name:'+b_name+', image-url:'+b_image+', banner-url:'+b_url+',\n');
			}
		});
		$('form#form-banner-data').submit((event)=>{
			let data = $('#banner-data').text();
			console.log(data)
		});
	});
</script>