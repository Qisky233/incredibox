<?php

$act = 'select-category';
if(isset($_POST['act'])){
	$act = $_POST['act'];
}

?>

<div class="section">
	<div class="alert alert-warning" role="alert">
		This action can't be undo.
	</div>
	<?php
	if($act == 'select-category'){
	?>
	<form id="plugin-select-category-src" method="POST">
		<input type="hidden" name="act" value="select-games">
		<div class="form-group">
			<p>Select game category.</p>
			<label class="form-label" for="category"><?php _e('Category') ?>:</label>
			<select class="form-control" name="cat_id" required/>
				<?php
					$results = array();
					$data = Category::getList();
					$categories = $data['results'];
					foreach ($categories as $cat) {
						echo '<option value="'.$cat->id.'"">'.ucfirst($cat->name).'</option>';
					}
				?>
			</select>
			<br>
			<button type="submit" class="btn btn-primary btn-md">OK</button>
		</div>
	</form>
	<?php
	} elseif($act == 'select-games'){
		$cat_id = (int)$_POST['cat_id'];
		$data = Category::getListByCategory( $cat_id, 1000 );
		$games = $data['results'];
		$total_game = $data['totalRows'];
		$row_size = 20;
	?>
	<?php echo esc_int($total_game)?> <?php echo '"'.Category::getById($cat_id)->name.'"'; ?> game<?php echo ( $total_game != 1 ) ? 's' : '' ?> found. (max: 1000)<br><br>
	<form id="plugin-move-games" method="POST">
		<input type="hidden" name="act" value="move-games">
		<div class="form-group">
			<label class="form-label" for="games">Games:</label>
			<select multiple class="form-control" name="games[]" size="<?php echo $row_size ?>" required/>
			<?php
				$index = 0;
				foreach ($games as $game) {
					$index++;
					echo '<option value="'.$game->id.'">'. $index .'. '. $game->title .' - ( '. $game->source .' )</option>';
				}
			?>
			</select>
		</div>
		<label class="form-label" for="category"><?php _e('Move to category') ?>:</label>
		<select class="form-control" name="cat_id" required/>
			<?php
				$results = array();
				$data = Category::getList();
				$categories = $data['results'];
				foreach ($categories as $cat) {
					if($cat->id != $cat_id){
						echo '<option value="'.$cat->id.'"">'.ucfirst($cat->name).'</option>';
					}
				}
			?>
		</select>
		<input type="hidden" name="cat_src_id" value="<?php echo $cat_id ?>" />
		<br>
		<div class="form-check">
			<input type="checkbox" class="form-check-input" name="also_delete_category" id="check-delete">
			<label class="form-label" class="form-check-label" for="check-delete">Also delete origin category if empty</label>
		</div>
		<br>
		<button type="submit" class="btn btn-primary btn-md">Move games</button>
	</form>
	<?php
	} elseif($act == 'move-games'){
		$game_ids = $_POST['games'];
		$cat_id = (int)$_POST['cat_id'];
		$cat_src_id = (int)$_POST['cat_src_id'];
		$src_cat_name = Category::getById($cat_src_id)->name;
		$target_cat_name = Category::getById($cat_id)->name;
		foreach ($game_ids as $id) {
			$game = Game::getById((int)$id);
			$arr_category = commas_to_array($game->category);
			for($i = 0; $i < count($arr_category); $i++){
				if($arr_category[$i] == $src_cat_name){
					$arr_category[$i] = $target_cat_name;
				}
			}
			$arr_category = array_unique($arr_category);
			$game->category = implode(",", $arr_category);
			$game->update();
		}
		show_alert(count($game_ids).' games from '.$src_cat_name.' moved to '.$target_cat_name, 'success');
		if(isset($_POST['also_delete_category'])){
			$cat = Category::getById($cat_src_id);
			if($cat->getCategoryCount($cat_src_id) == 0){
				echo '<br><b>Category "'.$cat->name.'" deleted!</b>';
				$cat->delete();
			}
		}
	}
	?>
</div>