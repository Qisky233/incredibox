<?php

$data = get_game_list('new', 1000);
$games = $data['results'];
$total_game = $data['totalRows'];
$row_size = 20;

?>

<div class="section">
	<?php echo esc_int($total_game)?> game<?php echo ( $total_game != 1 ) ? 's' : '' ?> found. (max: 1000)<br><br>
	<div class="alert alert-warning" role="alert">
		Keep in mind, removed games can't be recovered. please double check at it.
	</div>
	<form id="plugin-remove-game">
		<div class="form-group">
			<label for="games">Games:</label>
			<select multiple class="form-control" name="game" size="<?php echo $row_size ?>" required/>
			<?php
				$index = 0;
				foreach ($games as $game) {
					$index++;
					echo '<option value="'.$game->id.'">'. $index .'. '. $game->title .' - ( '. $game->source .' )</option>';
				}
			?>
			</select>
		</div>
		<button type="submit" class="btn btn-danger btn-md">Remove</button>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$( "form" ).submit(function( event ) {
			let arr = $( this ).serializeArray();
			let total = arr.length;
			let count = 0;
			if($(this).attr('id') === 'plugin-remove-game'){
				event.preventDefault();
				if(confirm('Are you sure want to delete '+total+' games?')){
					for(let i=0; i<total; i++){
						let data = {
							action: 'deleteGame',
							id: arr[i].value,
						}
						$.ajax({
							url: 'request.php',
							type: 'POST',
							dataType: 'json',
							data:data,
							complete: function (data) {
								console.log(data.responseText);
								count++;
								if(count === total){
									location.reload();
								}
							}
						});
					}
				}
			}
		});
	})
</script>