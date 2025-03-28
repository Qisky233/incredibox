	<!-- <footer class="footer text-center">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 mb-5 mb-lg-0">
					<?php widget_aside('footer-1') ?>
				</div>
				<div class="col-lg-4 mb-5 mb-lg-0">
					<?php widget_aside('footer-2') ?>
				</div>
				<div class="col-lg-4">
					<?php widget_aside('footer-3') ?>
				</div>
			</div>
		</div>
	</footer> -->
	<div class="copyright py-4 text-center text-white">
		<div class="container">
			<p>
				<a href="<?php echo DOMAIN; ?>privacy" class="text-white">Privacy</a> | 
				<a href="<?php echo DOMAIN; ?>sitemap.xml" class="text-white">SiteMap</a>
			</p>
			<?php
			if(isset($stored_widgets['footer-copyright'])){
				widget_aside('footer-copyright');
			} else {
				echo SITE_TITLE . ' © '.date('Y').'. All rights reserved.';
			}
			?>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/jquery-3.6.2.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/lazysizes.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN ?>js/comment-system.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/script.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/custom.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN ?>js/stats.js"></script>
	<?php load_plugin_footers() ?>
  </body>
</html>