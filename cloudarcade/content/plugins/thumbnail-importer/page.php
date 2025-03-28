<?php

if( !USER_ADMIN && ADMIN_DEMO ){
	die();
}

if(isset($_POST['action'])){
	if($_POST['action'] == 'fix-black'){
		$use_webp = get_option('webp-thumbnail');
		foreach ($_POST['game'] as $id) {
			$game = Game::getById($id);
			if( SMALL_THUMB && $game ){
				$thumb_2 = $game->thumb_2;
				$output = pathinfo($thumb_2);
				$thumb_small = '/thumbs/'.$game->slug.'_small.'.$output['extension'];
				$file_extension = pathinfo($thumb_2, PATHINFO_EXTENSION);
				if($file_extension != 'webp'){
					$use_webp = false;
				}
				if($use_webp){
					$thumb_small = str_replace('.'.$file_extension, '.webp', $thumb_small);
					generate_small_thumbnail($game->thumb_2, $game->slug);
				} else {
					generate_small_thumbnail($game->thumb_2, $game->slug);
				}
				$game->thumb_small = $thumb_small;
				$game->update();
			}
		}
	} else if($_POST['action'] == 'unset-missing-small'){
		foreach ($_POST['game'] as $id) {
			$game = Game::getById($id);
			$game->thumb_small = '';
			$game->update();
		}
	}
}

function is_black_image($image_path) {
    $file_extension = pathinfo($image_path, PATHINFO_EXTENSION);
    switch (strtolower($file_extension)) {
        case "png":
            $img = imagecreatefrompng($image_path);
            break;
        case "jpeg":
        case "jpg":
            $img = imagecreatefromjpeg($image_path);
            break;
        case "webp":
            $img = imagecreatefromwebp($image_path);
            break;
        default:
            return false;
    }
    $width = imagesx($img);
    $height = imagesy($img);
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            if ($r !== 0 || $g !== 0 || $b !== 0) {
                // Image is not black
                return false;
            }
        }
    }
    // Image is black
    return true;
}

?>

<div class="section">
	<?php
		if(!IMPORT_THUMB){
			show_alert('Import Thumbnails option must be activated!', 'warning');
			echo '<p>Import Thumbnails option located at Settings > Advanced.</p>';
		}
	?>
	<p>This plugin is used to import all external thumbnails to stored locally</p>
	<?php if(!isset($_POST['action'])){ ?>
	<form method="post" enctype="multipart">
		<input type="hidden" name="action" value="get_list">
		<button type="submit" class="btn btn-primary btn-md">Get List</button>
	</form>
	<div class="mb-5"></div>
	<p>Generate small thumbanail only</p>
	<form method="post" enctype="multipart">
		<input type="hidden" name="action" value="get_list_small">
		<button type="submit" class="btn btn-secondary btn-md">Get List</button>
	</form>
	<div class="mb-4"></div>
	<h3>Fixer</h3>
	<div class="mb-3"></div>
	<p>Fix black/blank/missing thumbnail</p>
	<form method="post" enctype="multipart">
		<input type="hidden" name="action" value="get_list_black">
		<button type="submit" class="btn btn-secondary btn-md">Get List</button>
	</form>
	<div class="mb-3"></div>
	<p>Unset missing small thumbnail</p>
	<form method="post" enctype="multipart">
		<input type="hidden" name="action" value="get_missing_small">
		<button type="submit" class="btn btn-secondary btn-md">Get List</button>
	</form>
	<?php } elseif($_POST['action'] == 'get_list'){
		$conn = open_connection();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM games WHERE LEFT(thumb_1, 4) = :http LIMIT 20';
		$st = $conn->prepare($sql);
		$st->bindValue(":http", 'http', PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetchAll(PDO::FETCH_ASSOC);
		//
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch()[0];
		if(!$totalRows){
			echo('<h3>All game thumbnails already stored locally.</h3>');
		} else {
			$ids = [];
			echo '<h3>'.count($row).' of '.$totalRows.' games found!</h3>';
			?>
			<div class="mb-3"></div>
			<form method="post" enctype="multipart" id="form-import-thumb">
				<input type="hidden" name="action" value="get_list">
			<?php
			foreach($row as $game){
				$ids[] = $game['id'];
				?>
				<div class="form-check" id="<?php echo 'd_id-'.$game['id'] ?>">
					<input class="form-check-input" type="checkbox" name="game[]" value="<?php echo $game['id'] ?>" id="<?php echo 'g_id-'.$game['id'] ?>" checked>
					<label class="form-check-label" for="<?php echo 'g_id-'.$game['id'] ?>">
						<?php echo $game['source'].' > '.$game['title'] ?>
					</label>
				</div>
				<?php
			}
			?>	
				<div class="mb-3"></div>
				<?php if(IMPORT_THUMB){ ?>
				<button type="submit" class="btn btn-primary btn-md" id="btn-import-thumb">Import All</button>
				<?php } ?>
			</form>
			<div class="mt-4 d-none" id="thumb-import-status">(On progress) 10 / 100 imported</div>
			<button class="btn btn-primary btn-md mt-4 d-none" id="btn-reload">Reload</button>
			<?php
		}
	} elseif($_POST['action'] == 'get_list_small'){
		if(SMALL_THUMB){
			$conn = open_connection();
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM games WHERE thumb_small = :str LIMIT 20';
			$st = $conn->prepare($sql);
			$st->bindValue(":str", '', PDO::PARAM_STR);
			$st->execute();
			$row = $st->fetchAll(PDO::FETCH_ASSOC);
			//
			$sql = "SELECT FOUND_ROWS() AS totalRows";
			$totalRows = $conn->query($sql)->fetch()[0];
			if(!$totalRows){
				echo('<h3>All games already have small thumbnail.</h3>');
			} else {
				$ids = [];
				echo '<h3>'.count($row).' of '.$totalRows.' games found!</h3>';
				?>
				<div class="mb-3">
					<p>Game's thumbnails must be store locally first.</p>
				</div>
				<div class="mb-3"></div>
				<form method="post" enctype="multipart" id="form-import-thumb-small">
				<?php
				foreach($row as $game){
					$ids[] = $game['id'];
					?>
					<div class="form-check" id="<?php echo 'd_id-'.$game['id'] ?>">
						<input class="form-check-input" type="checkbox" name="game[]" value="<?php echo $game['id'] ?>" id="<?php echo 'g_id-'.$game['id'] ?>" checked>
						<label class="form-check-label" for="<?php echo 'g_id-'.$game['id'] ?>">
							<?php echo $game['source'].' > '.$game['title'] ?>
						</label>
					</div>
					<?php
				}
				?>	
					<div class="mb-3"></div>
					<?php if(IMPORT_THUMB){ ?>
					<button type="submit" class="btn btn-primary btn-md" id="btn-import-thumb">Generate All</button>
					<?php } ?>
				</form>
				<div class="mt-4 d-none" id="thumb-import-status">(On progress) 10 / 100 generated</div>
				<button class="btn btn-primary btn-md mt-4 d-none" id="btn-reload">Reload</button>
				<?php
			}
		} else {
			show_alert('Small Thumbnails option must be activated!', 'warning');
			echo '<p>Small Thumbnails option located at Settings > Advanced.</p>';
		}
	} elseif($_POST['action'] == 'get_list_black'){
		if(SMALL_THUMB){
			$conn = open_connection();
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM games WHERE thumb_small != :str ORDER BY id DESC LIMIT 1000';
			$st = $conn->prepare($sql);
			$st->bindValue(":str", '', PDO::PARAM_STR);
			$st->execute();
			$row = $st->fetchAll(PDO::FETCH_ASSOC);
			//
			$sql = "SELECT FOUND_ROWS() AS totalRows";
			$totalRows = $conn->query($sql)->fetch()[0];
			$game_count = 0;
			if(!$totalRows){
				echo('<h3>Empty.</h3>');
			} else {
				$ids = [];
				echo '<h3>'.count($row).' games iterated</h3>';
				?>
				<div class="mb-3"></div>
				<form method="post" enctype="multipart" id="form-fix-black">
					<input type="hidden" name="action" value="fix-black">
				<?php
				foreach($row as $game){
					if($game['thumb_small'] && $game['thumb_small'] != '' && substr($game['thumb_small'], 0, 4) != 'http'){
						//
					} else {
						continue;
					}
					$ids[] = $game['id'];
					$image_missing = false;
					if(substr($game['thumb_small'], 0, 6) == '/thumb'){
						if(!file_exists('..'.$game['thumb_small'])){
							$image_missing = true;
						}
					}
					if(substr($game['thumb_small'], 0, 6) == '/games'){ // For self upload
						if(!file_exists('..'.$game['thumb_small'])){
							$image_missing = true;
						}
					}
					$file_size;
					if($image_missing){
						$file_size = 0;
					} else {
						$file_size = filesize('..'.$game['thumb_small']);
					}
					if($file_size <= 3000) { // Less than 3kb
						$is_black_image = false;
						if($image_missing){
							$is_black_image = true;
						} else {
							$is_black_image = is_black_image('..'.$game['thumb_small']);
						}
						if($is_black_image){
							$game_count++;
						?>
						<div class="form-check" id="<?php echo 'd_id-'.$game['id'] ?>">
							<input class="form-check-input" type="checkbox" name="game[]" value="<?php echo $game['id'] ?>" id="<?php echo 'g_id-'.$game['id'] ?>" checked>
							<img src="<?php echo $game['thumb_small'] ?>" class="gamelist" width="60px" height="auto">
							<label class="form-check-label" for="<?php echo 'g_id-'.$game['id'] ?>">
								<?php echo $game['source'].' > '.$game['title'] ?>
							</label>
						</div>
						<?php
						}
					}
				}
				?>	
					<div class="mb-3"></div>
					<?php if(IMPORT_THUMB && $game_count){ ?>
					<button type="submit" class="btn btn-primary btn-md" id="btn-fix-black">Fix All</button>
					<?php } ?>
				</form>
				<?php
			}
		} else {
			show_alert('Small Thumbnails option must be activated!', 'warning');
			echo '<p>Small Thumbnails option located at Settings > Advanced.</p>';
		}
	}  elseif($_POST['action'] == 'get_missing_small'){
		if(SMALL_THUMB){
			$conn = open_connection();
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM games WHERE thumb_small != :str ORDER BY id DESC LIMIT 1000';
			$st = $conn->prepare($sql);
			$st->bindValue(":str", '', PDO::PARAM_STR);
			$st->execute();
			$row = $st->fetchAll(PDO::FETCH_ASSOC);
			//
			$sql = "SELECT FOUND_ROWS() AS totalRows";
			$totalRows = $conn->query($sql)->fetch()[0];
			$game_count = 0;
			if(!$totalRows){
				echo('<h3>Empty.</h3>');
			} else {
				$ids = [];
				echo '<h3>'.count($row).' games iterated</h3>';
				?>
				<div class="mb-3"></div>
				<form method="post" enctype="multipart">
					<input type="hidden" name="action" value="unset-missing-small">
				<?php
				foreach($row as $game){
					$ids[] = $game['id'];
					$image_missing = false;
					if(substr($game['thumb_small'], 0, 1) == '/'){
						if(!file_exists('..'.$game['thumb_small'])){
							$image_missing = true;
						}
					}
					if($image_missing) {
						if(true){
							$game_count++;
						?>
						<div class="form-check" id="<?php echo 'd_id-'.$game['id'] ?>">
							<input class="form-check-input" type="checkbox" name="game[]" value="<?php echo $game['id'] ?>" id="<?php echo 'g_id-'.$game['id'] ?>" checked>
							<img src="<?php echo $game['thumb_small'] ?>" class="gamelist" width="60px" height="auto">
							<label class="form-check-label" for="<?php echo 'g_id-'.$game['id'] ?>">
								<?php echo $game['source'].' > '.$game['title'] ?>
							</label>
						</div>
						<?php
						}
					}
				}
				?>	
					<div class="mb-3"></div>
					<?php if(IMPORT_THUMB && $game_count){ ?>
					<button type="submit" class="btn btn-primary btn-md">Unset All</button>
					<?php } ?>
				</form>
				<?php
			}
		} else {
			show_alert('Small Thumbnails option must be activated!', 'warning');
			echo '<p>Small Thumbnails option located at Settings > Advanced.</p>';
		}
	}
	?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#btn-reload').click(()=>{
			location.reload();
		});
		$( "#form-import-thumb" ).submit(function( event ) {
			event.preventDefault();
			let arr = $( this ).serializeArray();
			let list = [];
			let count = 0;
			let total = 0;
			arr.forEach((item)=>{
				if(item['name'] == 'game[]'){
					list.push(item.value);
				}
			});
			total = list.length;
			$('#thumb-import-status').removeClass('d-none');
			$('#thumb-import-status').text(count + '/' + total + ' imported');
			$('#btn-import-thumb').prop('disabled', true);
			$('#btn-import-thumb').text('IMPORTING...');
			pre_import();
			function pre_import(){
				if(list.length){
					import_thumb_plugin(list[0], 'import').then((res)=>{
						if(res == 'ok'){
							count++;
							$('#thumb-import-status').text(count + '/' + total + ' imported');
							$('#d_id-'+list[0]).remove();
							list.shift();
							pre_import();
						} else {
							console.log(res);
							$('#thumb-import-status').text('Failed to complete all request');
							alert('Failed!');
						}
					});
				} else {
					if(count){
						$('#btn-reload').removeClass('d-none');
						alert('Completed!');
					}
				}
			}
		});
		$( "#form-import-thumb-small" ).submit(function( event ) {
			event.preventDefault();
			let arr = $( this ).serializeArray();
			let list = [];
			let count = 0;
			let total = 0;
			arr.forEach((item)=>{
				if(item['name'] == 'game[]'){
					list.push(item.value);
				}
			});
			total = list.length;
			$('#thumb-import-status').removeClass('d-none');
			$('#thumb-import-status').text(count + '/' + total + ' generated');
			$('#btn-import-thumb').prop('disabled', true);
			$('#btn-import-thumb').text('IMPORTING...');
			pre_import();
			function pre_import(){
				if(list.length){
					import_thumb_plugin(list[0], 'generate-small').then((res)=>{
						if(res == 'ok'){
							count++;
							$('#thumb-import-status').text(count + '/' + total + ' generated');
							$('#d_id-'+list[0]).remove();
							list.shift();
							pre_import();
						} else {
							console.log(res);
							$('#thumb-import-status').text('Failed to complete all request');
							alert('Failed!');
						}
					});
				} else {
					if(count){
						$('#btn-reload').removeClass('d-none');
						alert('Completed!');
					}
				}
			}
		});
		function import_thumb_plugin(id, action){
			let wait = new Promise((res) => {
				$.ajax({
					url: '../content/plugins/thumbnail-importer/action.php',
					type: 'POST',
					dataType: 'json',
					data: {action: action, id: id},
					success: function (data) {
						//console.log(data.responseText);
					},
					error: function (data) {
						//console.log(data.responseText);
					},
					complete: function (data) {
						res(data.responseText);
					}
				});
			});
			return wait;
		}
	});
</script>