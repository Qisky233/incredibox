<div id="action-info"></div>
<div class="section">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Sender</th>
					<th>Date</th>
					<th>Game</th>
					<th>Comment</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$index = 0;
				$conn = open_connection();
				$sql = "SELECT * FROM comments ORDER BY id DESC LIMIT 10000";
				$st = $conn->prepare($sql);
				$st->execute();
				$row = $st->fetchAll();
				if(count($row) > 0){
					foreach ( $row as $item ) {
						$index++;
						$data = $item;
						?>

						<tr id="tr-<?php echo esc_int($data['id']) ?>">
							<th scope="row"><?php echo $index ?></th>
							<td>
								<?php echo $data['sender_username'] ?>
							</td>
							<td>
								<?php echo $item['created_date'] ?>
							</td>
							<td>
								<?php
									$game = Game::getById($data['game_id']);
									if($game){
										echo $game->title;
									} else {
										_e('Game not exist!');
									}
								?>
							</td>
							<td>
								<?php echo $data['comment'] ?>
							</td>
							<td>
								<span class="actions">
									<a href="#" class="deleteComment" id="<?php echo esc_int($data['id']) ?>"><i class="fa fa-trash circle" aria-hidden="true"></i></a>
									<?php if($data['approved'] == 0){ ?>
										<a href="#" class="approveComment" id="<?php echo esc_int($data['id']) ?>"><i class="fa fa-check circle" aria-hidden="true"></i></a>
									<?php } ?>
								</span>
							</td>
						</tr>

						<?php
					}
				}
					
				?>
						
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.deleteComment').click(function(){
			let id = $(this).attr('id');
			$.ajax({
				url: '<?php echo DOMAIN ?>includes/comment.php',
				type: 'POST',
				dataType: 'json',
				data: {delete: true, id: id},
				complete: function (data) {
					console.log(data.responseText);
					if(data.responseText === 'deleted'){
						$('#action-info').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Comment deleted!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						$('#tr-'+id).remove();
					} else {
						$('#action-info').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">Failed! Check console log<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					}
				}
			});
		});
		$('.approveComment').click(function(){
			let curElement = $(this);
			let id = $(this).attr('id');
			$.ajax({
				url: '<?php echo DOMAIN ?>includes/comment.php',
				type: 'POST',
				dataType: 'json',
				data: {approve: true, id: id},
				complete: function (data) {
					console.log(data.responseText);
					if(data.responseText === 'ok'){
						$('#action-info').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Comment approved!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						curElement.remove();
					} else {
						$('#action-info').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">Failed! Check console log<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					}
				}
			});
		});
	});
</script>