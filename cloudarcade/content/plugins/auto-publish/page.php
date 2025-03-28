<?php

$list = array(
	'gamedistribution' => 'GameDistribution',
	'gamepix' => 'GamePix',
	'_gamemonetize' => 'GameMonetize',
	'_4j' => '4J',
	'_wanted5games' => 'Wanted5Games',
	'_gamearter' => 'GameArter',
);

$cron_data = get_option('cron-job');
$cron_status = 'Inactive';
if(!is_null($cron_data)){
	$cron_data = json_decode($cron_data, true);
	if(isset($cron_data['auto-post'])){
		$cron_status = 'Active';
	}
} else {
	$cron_data = [];
}

if(isset($_POST['action'])){
	$action = $_POST['action'];

	if($action == 'create'){
		$_arr = [];
		$arr = [];
		$i = 0;
		foreach ($list as $item => $value) {
			$i++;
			if($_POST['a'.$i] != ''){
				if(!isset($_arr[$_POST['a'.$i]])){
					$_arr[$_POST['a'.$i]] = 'p';
					$arr[$_POST['a'.$i]] = $_POST['b'.$i];
				}
			};
		}
		if(count($arr)){
			$cron_data['auto-post'] = [];
			$cron_data['auto-post']['list'] = $arr;
			$cron_data['auto-post']['date'] = date('Y-m-d H:i:s', strtotime('+5 seconds', strtotime(date('Y-m-d H:i:s'))));
			update_option('cron-job', json_encode($cron_data));
			$cron_status = 'Active';
		} else {
			show_alert('Fields is empty', 'danger');
		}
	} elseif($action == 'stop'){
		unset($cron_data['auto-post']);
		$cron_status = 'Inactive';
		update_option('cron-job', json_encode($cron_data));
	} elseif($action == 'clear-log'){
		if(file_exists(ABSPATH . PLUGIN_PATH . '/auto-publish/log.txt')){
			unlink(ABSPATH . PLUGIN_PATH . '/auto-publish/log.txt');
		}
	}
}

$callout_type = 'warning';

$active_distributors = [];
if($cron_status == 'Active'){
	$callout_type = 'info';
	foreach ($cron_data['auto-post']['list'] as $item => $value) {
		$active_distributors[$item] = 'p';
	}
}

?>

<div class="section">
	<div class="bs-callout bs-callout-<?php echo $callout_type ?>">
		<h5 style="font-weight: 400;"><?php _e('Status') ?>: <?php _e($cron_status) ?></h5>
		<?php if($cron_status == 'Inactive'){
			_e('Create one to start auto-publish game.');
		} else {
			$datetime1 = date_create(date("Y-m-d H:i:s"));
			$datetime2 = date_create($cron_data['auto-post']['date']);
			$interval = date_diff($datetime1, $datetime2);
			$diff = $interval->format('Next post: %h hours, %i minutes, and %s seconds.');
			echo $diff;
		}?>
	</div>
	<?php if(true) {
		?>
		<form id="form-auto-post" action="dashboard.php?viewpage=plugin&name=auto-publish" method="post">
			<input type="hidden" name="action" value="create">
			<div class="form-group">
				<div class="row">
					<div class="col">
						<label>Game Distributor:</label>
					</div>
					<div class="col">
						<label>Publisher ID (Optional):</label>
					</div>
				</div>
				<?php
				$index = 0;
				foreach ($list as $a) {
					$index++;
					$cur_dist = null;
				?>
				<div class="row">
					<div class="col">
						<select class="form-control" name="a<?php echo $index ?>" id="content-type">
							<?php
							$def_selected = 'selected';
							foreach ($list as $item => $value) {
								$selected = '';
								if(isset($active_distributors[$item]) && $def_selected == 'selected'){
									$selected = 'selected';
									$def_selected = '';
									$cur_dist = $item;
									unset($active_distributors[$item]);
								}
								echo '<option value="'.$item.'" '.$selected.'>'.$value.'</option>';
							}

							?>
							<option value="" <?php echo $def_selected ?>>- None</option>
						</select>
					</div>
					<div class="col">
						<?php
						$val = '';
						if(!is_null($cur_dist)){
							$val = $cron_data['auto-post']['list'][$cur_dist];
						}
						?>
						<input type="text" class="form-control key-name" name="b<?php echo $index ?>" value="<?php echo $val ?>">
					</div>
				</div>
				<div class="mb-3"></div>
				<?php
				}
				?>
			</div>
			<input type="submit" class="btn btn-primary btn-md" value="<?php _e('CREATE') ?>">
		</form>
		<br>
		<?php

		if($cron_status == 'Active'){
			?>
			<form id="form-auto-post-stop" action="dashboard.php?viewpage=plugin&name=auto-publish" method="post">
				<input type="hidden" name="action" value="stop">
				<input type="submit" class="btn btn-danger btn-md" value="<?php _e('STOP') ?>">
			</form>
			<?php
		}

		if(file_exists(ABSPATH . PLUGIN_PATH . '/auto-publish/log.txt')){
			// Have log.txt
			$log_txt = file_get_contents(ABSPATH . PLUGIN_PATH . '/auto-publish/log.txt');
			?>
			<div class="form-group">
				<label for="log-txt">Log:</label>
				<textarea class="form-control" id="log-txt" rows="6" readonly><?php echo $log_txt ?></textarea>
			</div>
			<form method="post">
				<input type="hidden" name="action" value="clear-log">
				<input type="submit" class="btn btn-info btn-md" value="<?php _e('Clear Log') ?>">
			</form>
			<?php
		}

		?>
	<?php }  ?>
</div>