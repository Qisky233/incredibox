<?php

if(isset($_POST['action'])){
	if($_POST['action'] == 'update-tag'){
		if(isset($_POST['extra_fields'])){
			if(is_array($_POST['extra_fields'])){
				$_POST['extra_fields'] = json_encode($_POST['extra_fields']);
			}
			$st = $conn->prepare("UPDATE tags SET extra_fields = :extra_fields WHERE name = :name LIMIT 1");
			$st->bindValue(":name", $_POST['tag-name'], PDO::PARAM_STR);
			$st->bindValue(":extra_fields", $_POST['extra_fields'], PDO::PARAM_STR);
			$st->execute();
			show_alert('Tag fields updated!', 'success');
		}
	}
}

?>
<div id="action-info" style="display:none;">
	<?php show_alert('Tag deleted!', 'success') ?>
</div>
<div class="section">
	<?php
	if(isset($_GET['edit']) && isset($_GET['tag-name'])){
		?>
		<div class="action-btn">
			<a href="dashboard.php?viewpage=plugin&name=tag-manager">
				<button class="btn btn-primary btn-sm">Back to tag list</button>
			</a>
		</div>
		<?php
		$st = $conn->prepare("SELECT * FROM tags WHERE name = :name LIMIT 1");
		$st->bindValue(":name", $_GET['tag-name'], PDO::PARAM_STR);
		$st->execute();
		$tag = $st->fetch(PDO::FETCH_ASSOC);
		if($tag){
			?>
			<div class="mb-3"></div>
			<h5>Tag name: <?php echo $tag['name'] ?></h5>
			<div class="mb-3"></div>
			<div class="row">
				<div class="col-md-6">
					<?php
					$extra_fields = get_extra_fields('tag');
					if(count($extra_fields)){
						?>
						<form method="post">
							<input type="hidden" name="action" value="update-tag">
							<input type="hidden" name="tag-name" value="<?php echo $tag['name'] ?>">
							<div class="extra-fields">
								<?php
								$tag_fields = json_encode($tag['extra_fields'], true);
								foreach ($extra_fields as $field) {
									?>
									<div class="mb-3">
										<label class="form-label" for="<?php echo $field['field_key'] ?>"><?php _e($field['title']) ?>:
											<br>
											<small class="fst-italic text-secondary"><?php echo $field['field_key'] ?></small>
										</label>
										<?php
										$default_value = get_tag_extra_field($tag, $field['field_key']);
										$placeholder = $field['placeholder'];
										if($field['type'] === 'textarea'){
											echo '<textarea class="form-control" name="extra_fields['.$field['field_key'].']" rows="3">'.$default_value.'</textarea>';
										} else if($field['type'] === 'number'){
											echo '<input type="number" name="extra_fields['.$field['field_key'].']" class="form-control" placeholder="'.$placeholder.'" value="'.$default_value.'">';
										} else if($field['type'] === 'text'){
											echo '<input type="text" name="extra_fields['.$field['field_key'].']" class="form-control" placeholder="'.$placeholder.'" value="'.$default_value.'">';
										}
										?>
									</div>
									<?php
								}
								?>
							</div>
							<div class="mb-3"></div>
							<input type="submit" class="btn btn-primary" value="Save">
						</form>
						<?php
					} else {
						echo '<p>No extra fields found for the tag!</p>';
					}
					?>
				</div>
			</div>
			<?php
		}
	} else {
	?>
		<div class="action-btn">
			<button class="btn btn-primary btn-sm" id="update-tag">Update tag usage</button>
		</div>
		<div class="mb-4"></div>
		<div class="tag-list">
			<div class="row">
			<?php
			$tags = get_tags('usage', 1000);
			if(count($tags)){
				foreach ($tags as $tag_name) {
					echo '<div class="col-md-3 col-6" id="tag-'.$tag_name.'">';
					echo '<div class="btn btn-secondary btn-sm mb-2 item-tag">';
					echo $tag_name;
					echo '<span class="badge bg-light text-dark">'.get_tag_usage($tag_name).'</span>';
					echo '</div>';
					echo '<a href="dashboard.php?viewpage=plugin&name=tag-manager&edit&tag-name='.$tag_name.'">';
					echo '<span class="badge bg-primary edit-tag"><i class="fas fa-edit"></i></span>';
					echo '</a>';
					echo '<span class="badge bg-danger delete-tag" data-name="'.$tag_name.'"><i class="fas fa-times"></i></span>';
					echo '</div>';
				}
			} else {
				echo '<h3>'._t('No tags').'</h3>';
			}
			?>
			</div>
		</div>
	<?php } ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.delete-tag').click(function(){
			let name = $(this).attr('data-name');
			if(confirm('Are you sure want to delete "'+name+'" tag?')){
				$('#action-info').hide();
				$.ajax({
					url: '../content/plugins/tag-manager/action.php',
					type: 'POST',
					dataType: 'json',
					data: {action: 'delete', name: name},
					complete: function (data) {
						console.log(data.responseText);
						if(data.responseText === 'deleted'){
							$('#action-info').show();
							$('#tag-'+name).remove();
						} else {
							alert('Error, check console log for more info!');
						}
					}
				});
			}
		});
		$('#update-tag').click(function(){
			$.ajax({
				url: '../content/plugins/tag-manager/action.php',
				type: 'POST',
				dataType: 'json',
				data: {action: 'update-tag-usage'},
				complete: function (data) {
					console.log(data.responseText);
					if(data.responseText === 'ok'){
						location.reload();
					} else {
						alert('Error, check console log for more info!');
					}
				}
			});
		});
	});
</script>