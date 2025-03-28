<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="game-container">
		<?php widget_aside('top-content') ?>
		<!-- NEW GAMES -->
		<h3 class="item-title"><i class="fa fa-solid fa-gamepad" aria-hidden="true"></i><?php _e('NEW GAMES') ?></h3>
		<div class="row" id="section-new-games">
			<?php
			$games = fetch_games_by_type('new', 18, 0, false)['results'];
			foreach ( $games as $game ) { ?>
				<?php include  TEMPLATE_PATH . "/includes/grid.php" ?>
			<?php } ?>
		</div>
		<!-- Load more games -->
		<div class="load-more-games-wrapper">
			<!-- Template -->
			<div class="item-append-template" style="display: none;">
				<div class="col-md-2 col-sm-3 col-4 item-grid">
					<a href="<?php echo get_permalink('game') ?>{{slug}}">
					<div class="list-game">
						<div class="list-thumbnail"><img src="<?php echo get_template_path(); ?>/images/thumb-placeholder1.png" data-src="{{thumbnail}}" class="small-thumb lazyload" alt="{{title}}"></div>
						<div class="list-content">
							<div class="list-title">{{title}}</div>
						</div>
					</div>
					</a>
				</div>
			</div>
			<!-- The button -->
			<div class="btn btn-primary btn-load-more-games">
				<?php _e('Load more games') ?> <i class="fa fa-chevron-down" aria-hidden="true"></i>
			</div>
		</div>
		
		<h3 class="item-title"><i class="fa fa-solid fa-hashtag" aria-hidden="true"></i><?php _e('Site Intro') ?></h3>
		<div>
			<h3>The Ultimate Fan-Made Music Experience</h3>
			<p>The innovative Incredibox Mustard has revolutionized how fans interact with music creation. As a standout modification of the original Incredibox platform, Incredibox Mustard brings fresh energy to the beloved music game. What makes Incredibox Mustard special is its unique approach to sound mixing and visual design, created with passion by dedicated community members.</p>
			<h3>The Evolution of Incredibox Mustard</h3>
			<p>Since its inception, Incredibox Mustard has grown from a simple mod to a comprehensive music creation platform. The development team behind Incredibox Mustard focused on creating an interface that balances accessibility with depth. Every update to Incredibox Mustard has brought new features that enhance the user experience, making it a favorite among both beginners and experienced music creators.</p>
			<h3>Comprehensive Sound Library in Incredibox Mustard</h3>
			<p>The sound library in Incredibox Mustard spans multiple genres and styles. From deep bass lines to crisp hi-hats, Incredibox Mustard provides creators with a vast array of musical elements. Each sound in Incredibox Mustard has been carefully crafted to ensure perfect harmony when mixed with others, allowing for countless creative combinations.</p>
		</div>
		<?php widget_aside('bottom-content') ?>
	</div>
	<div class="mb-4 mt-4 hp-bottom-container">
		<?php widget_aside('homepage-bottom') ?>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>