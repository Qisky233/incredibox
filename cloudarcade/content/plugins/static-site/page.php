<?php

if( !USER_ADMIN && ADMIN_DEMO ){
	die();
}

$is_debug = false;
$is_active = false;
$disabled = '';

if(isset($_POST['action'])){
	$action = $_POST['action'];
	if($action == 'enable_debug'){
		file_put_contents(dirname(__FILE__).'/debug.txt', '');
	}
	if($action == 'disable_debug'){
		if(file_exists(dirname(__FILE__).'/debug.txt')){
			unlink(dirname(__FILE__).'/debug.txt');
		}
	}
	if($action == 'remove_static_site'){
		if(file_exists('../static/')){
			delete_files('../static/');
		}
		if(file_exists('../static/')){
			unlink('../static/');
		}
		if(file_exists('../index_static.php')){
			unlink('../index_static.php');
		}
	}
	if($action == 'activate'){
		if(!file_exists('../static/')){
			mkdir('../static/', 0755, true);
		}
		if(!file_exists('../index_static.php')){
			copy(ABSPATH.PLUGIN_PATH.'static-site/index_static.php', '../index_static.php');
		}
	}
}

if(file_exists(dirname(__FILE__).'/debug.txt')){
	$is_debug = true;
	show_alert('Debug is active!', 'warning');
}

if(file_exists(ABSPATH.'static/') && file_exists(ABSPATH.'index_static.php')){
	$is_active = true;
} else {
	$disabled = 'disabled';
}

?>
<div class="section">
	<div class="p-section">
	<p>Guide: <a href="https://cloudarcade.net/tips/static-site/" target="_blank">How to use Static Site plugin</a></p>
	<div id="p-status" class="d-none">
		<h4>PROCESSING...</h4>
		<div class="progress">
  			<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
		</div>
	</div>
	<div id="p-notif" class="d-none">
		<div class="alert alert-primary" role="alert" id="p-alert">TEST</div>
	</div>
	<hr>
	<h4>PRIMARY</h4>
	<?php if(!$is_active) { ?>
	<form method="post" action="">
		<input type="hidden" name="action" value="activate">
		<input type="submit" class="btn btn-success btn-md" value="activate static site">
	</form>
	<?php } ?>
	<form class="p-ss">
		<input type="hidden" name="action" value="all">
		<input type="submit" class="btn btn-primary btn-md" value="Generate whole site">
		(Warning: CPU intensive task)
	</form>
	<hr>
	<h4>SPECIFIC</h4>
	<form class="p-ss">
		<input type="hidden" name="action" value="new_content">
		<input type="submit" class="btn btn-primary btn-md" value="Update new content" <?php echo $disabled ?>>
	</form>
	<form class="p-ss">
		<input type="hidden" name="action" value="games">
		<input type="submit" class="btn btn-primary btn-md" value="Update all games" <?php echo $disabled ?>>
	</form>
	<form class="p-ss">
		<input type="hidden" name="action" value="categories">
		<input type="submit" class="btn btn-primary btn-md" value="Update categories" <?php echo $disabled ?>>
	</form>
	<form class="p-ss">
		<input type="hidden" name="action" value="pages">
		<input type="submit" class="btn btn-primary btn-md" value="Update pages" <?php echo $disabled ?>>
	</form>
	<form class="p-ss">
		<input type="hidden" name="action" value="posts">
		<input type="submit" class="btn btn-primary btn-md" value="Update posts" <?php echo $disabled ?>>
	</form>
	
	<hr>
	<h4>CUSTOM</h4>
	<form class="form-inline p-ss" id="form-single-url">
		<div class="form-group">
			<input type="hidden" name="action" value="single_url">
			<label><?php echo DOMAIN ?></label><div class="mr-2"></div>
			<input type="text" class="form-control" name="url" placeholder="game/drop-it/">
		</div>
		<input type="submit" class="btn btn-primary btn-md" value="UPDATE URL" <?php echo $disabled ?>>
	</form>
	<hr>
	<h4>ACTION</h4>
	<?php if($is_active){ ?>
	<form method="post" action="">
		<input type="hidden" name="action" value="remove_static_site">
		<input type="submit" class="btn btn-danger btn-md" value="remove static site">
	</form>
	<?php } ?>
	<form method="post" action="">
		<?php if($is_debug){ ?>
		<input type="hidden" name="action" value="disable_debug">
		<input type="submit" class="btn btn-success btn-md" value="disable debug" <?php echo $disabled ?>>
		<?php } else { ?>
		<input type="hidden" name="action" value="enable_debug">
		<input type="submit" class="btn btn-danger btn-md" value="enable debug" <?php echo $disabled ?>>
		<?php } ?>
	</form>

	<hr>
	<h4>INTERVAL UPDATE</h4>
	<p>
		Min value: 1. Max value: 5
	</p>
	<form id="p-interval" class="form-inline p-ss">
		<label>Seconds: </label><div class="mr-2"></div>
		<input type="number" value="2" name="interval" id="val-interval">
		<input type="submit" class="btn btn-primary btn-md" id="b-start" value="Start interval" <?php echo $disabled ?>>
	</form>
	</div>
	<button class="btn btn-danger btn-md d-none" id="b-stop">Stop interval</button>
	<hr>
	<h4>LOG</h4>
	<textarea rows="4" id="error-log" class="form-control"></textarea>
</div>
<script type="text/javascript">
	var _interval;
	var _cur_intv = 0;
	$(document).ready(()=>{
		$( "form.p-ss" ).submit(function( event ) {
			event.preventDefault();
			$("#p-status").removeClass('d-none');
			$("#p-notif").addClass('d-none');
			let section = $(".p-section");
			section.css('opacity', '0.5');
			section.css('pointer-events', 'none');
			let arr = $( this ).serializeArray();
			let params = {};
			arr.forEach((item)=>{
				params[item.name] = item.value;
			});
			if($(this).attr('id') == 'form-single-url'){
				if(params.action){
					if(params.url[params.url.length-1] != '/'){
						params.url = params.url + '/';
					}
				}
			}
			let skip = false;
			if($(this).attr('id') == 'p-interval'){
				skip = true;
				let intv = $("#val-interval").val();
				if(intv < 1){
					intv = 1;
				}
				if(intv > 5){
					intv = 5;
				}
				let b_stop = $("#b-stop");
				b_stop.removeClass('d-none');
				$.ajax({
					url: '../content/plugins/static-site/get-list.php',
					type: 'POST',
					dataType: 'json',
					data: params,
					complete: function (data) {
						try {
							data = JSON.parse(data.responseText);
							start_interval(intv, data);
						} catch {
							$('#error-log').val(data.responseText);
							alert('error!');
						}
					}
				});
			}
			if(!skip){
				$.ajax({
					url: '../content/plugins/static-site/generate.php',
					type: 'POST',
					dataType: 'json',
					data: params,
					complete: function (data) {
						console.log(data.responseText);
						let msg = '';
						let class_status = '';
						if(data.responseText == 'ok'){
							//$('div.b-'+id).addClass('d-none');
							//$('i.t-'+id).addClass('d-none');
							msg = 'Updated !';
							class_status = 'alert-success';
						} else {
							console.log(data.responseText);
							msg = 'There may some errors, check Log below !';
							class_status = 'alert-danger';
						}
						$("#p-status").addClass('d-none');
						$("#p-notif").removeClass('d-none');
						enable_section();
						let alert = $('#p-alert');
						alert.removeClass('alert-primary');
						alert.removeClass('alert-success');
						alert.removeClass('alert-danger');
						alert.addClass(class_status);
						alert.html(msg);
						$('#error-log').val(data.responseText);
					}
				});
			}
		});
		function enable_section(){
			let section = $(".p-section");
			section.css('opacity', '1');
			section.css('pointer-events', 'auto');
		}
		$("#b-stop").on('click', ()=>{
			stop_interval();
		});
		function stop_interval(){
			if(_interval){
				clearInterval(_interval);
			}
			$("#b-stop").addClass('d-none');
			enable_section();
		}
		function start_interval(delay, data){
			let total = data.length;
			delay = delay*1000;
			let _log = $('#error-log');
			if(_interval){
				clearInterval(_interval);
			}
			_interval = setInterval(()=>{
				let params = {
					action: 'single_url',
					url: data[_cur_intv]
				}
				$.ajax({
					url: '../content/plugins/static-site/generate.php',
					type: 'POST',
					dataType: 'json',
					data: params,
					complete: function (data) {
						if(data.responseText != 'ok'){
							$('#error-log').val(data.responseText);
							stop_interval();
							alert('error!');
							return;
						}
					}
				});
				_cur_intv++;
				let log_str = "Updating... "+_cur_intv+" / "+total;
				log_str += '\n'+str_delay_to_time(delay, _cur_intv, total);
				_log.val(log_str);
				if(_cur_intv >= total){
					stop_interval();
				}
			}, delay);
		}
		function str_delay_to_time(delay, cur, total){
			let remain = total-cur;
			delay = Math.round(delay/1000);
			let total_s = delay * remain;
			let hours = Math.floor(total_s / 3600);
			let minutes = total_s - (hours * 3600);
			if(minutes > 0){
				minutes = Math.floor(minutes / 60);
			} else {
				minutes = 0;
			}
			let seconds = total_s - (hours * 3600) - (minutes * 60);
			if(seconds < 0){
				seconds = 0;
			}
			return hours+' hour '+minutes+' minute '+seconds+' seconds remaining';
		}
	});
</script>