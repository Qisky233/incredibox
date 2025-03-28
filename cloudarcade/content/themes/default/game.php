<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="game-container">
		<?php widget_aside('top-content') ?>
		<div class="content-wrapper">
			<div class="row">
				<div class="col-md-9 game-content">
					<div class="game-iframe-container">
						<iframe class="game-iframe" id="game-area" src="<?php echo get_game_url($game); ?>" width="<?php echo esc_int($game->width); ?>" height="<?php echo esc_int($game->height); ?>" frameborder="0" allowfullscreen></iframe>
					</div>
					<div class="single-info-container">
						<div class="header-left">
							<h1 class="single-title"><?php echo htmlspecialchars( $game->title )?></h1>
							<p><?php _e('Played %a times.', esc_int($game->views)) ?></p>
						</div>
						<div class="header-right">
							<div class="stats-vote">
								<?php
									$vote_percentage = '- ';
									if($game->upvote+$game->downvote > 0){
										$vote_percentage = floor(($game->upvote/($game->upvote+$game->downvote))*100);
									}
								?>
								<div class="txt-stats"><b class="text-success"><?php echo $vote_percentage ?>%</b> (<?php echo $game->upvote ?>/<?php echo $game->upvote+$game->downvote ?>)</div>
								<?php if($login_user){
									$favorited_class = '';
									if(is_favorited_game($game->id)){
										$favorited_class = 'color-red';
									}
									?>
								<i class="icon-vote fa fa-heart <?php echo $favorited_class ?>" id="favorite" data-id="<?php echo $game->id ?>"></i>
								<?php } ?>
								<i class="icon-vote fa fa-thumbs-up" id="upvote" data-id="<?php echo $game->id ?>"></i>
								<i class="icon-vote fa fa-thumbs-down" id="downvote" data-id="<?php echo $game->id ?>"></i>
								<div class="vote-status"></div>
							</div>
						</div>
						<div class="action-btn">
							<?php
							if(defined('GAME_REPORTS')){
								?><div class="single-icon"><i class="fa fa-bug" aria-hidden="true"></i><a href="#" id="report-game"><?php _e('Report') ?></a></div>
								<?php
							}
							?>
						</div>
					</div>
					<b><?php _e('Description') ?>:</b>
					<div class="single-description">
						<?php echo nl2br( $game->description )?>
					</div>
					<br>
					<b><?php _e('Instructions') ?>:</b>
					<div class="single-instructions">
						<?php echo nl2br( $game->instructions )?>
					</div>
					<br>
					<?php if(can_show_leaderboard()) { ?>
					<div class="single-leaderboard">
						<div id="content-leaderboard" class="table-responsive" data-id="<?php echo $game->id ?>"></div>
					</div>
					<?php } ?>
					<br>
					<?php if(get_setting_value('comments')){
						echo '<div class="mt-4"></div>';
						echo '<b>'._t('Comments').':</b>';
						render_game_comments($game->id);
					} ?>
				</div>
				<div class="col-md-3">
					<?php include  TEMPLATE_PATH . "/parts/sidebar.php" ?>
				</div>
			</div>
		</div>
		<?php widget_aside('bottom-content') ?>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>