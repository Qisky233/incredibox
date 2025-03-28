<?php
	if(isset($_GET['status'])){
		if($_GET['status'] == 'uploaded'){
			show_alert('Image uploaded!', 'success', true);
		} else if($_GET['status'] == 'deleted'){
			show_alert('Image deleted!', 'warning', true);
		}
	}
	$cur_page = 1;
	if(isset($_GET['page'])){
		$cur_page = (int)$_GET['page'];
	}
?>
<style type="text/css">
	.image-gallery-plugin {
		display: flex;
		flex-wrap: wrap;
	}
	.image-gallery-item {
		width: 150px;
		margin-right: 13px;
		margin-bottom: 13px;
		border: 1px solid #dfdfdf;
		overflow: hidden;
		border-radius: 10px;
		position: relative;
	}
	.item-name {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.thumb-container {
		width: 150px;
		height: 150px;
		position: relative;
		background-color: #eff1f4;
	}
	.item-thumbnail {
		max-height: 100%;
		max-width: 100%;
		padding: 0;
		border: 0;
		margin: auto;
		position: absolute;
		left: 50%;
		top: 50%;
		-webkit-transform: translateY(-50%) translateX(-50%);
	}
	.item-size, .item-dim {
		font-style: italic;
		font-size: 12px;
	}
	.item-info {
		padding: 10px;
	}
	.gallery-panel {
		margin-bottom: 20px;
	}
	.delete-image {
		color: #fff;
		background-color: #fd2828;
		position: absolute;
		top: 5px;
		right: 6px;
		z-index: 1;
		width: 40px;
		height: 40px;
		text-align: center;
		border-radius: 50%;
		display: none;
	}
	.delete-image i {
		margin-top: 12px;
	}
	.image-gallery-item:hover > .delete-image {
		display: block;
	}
	.image-gallery-item:hover {
		box-shadow: 0 3px 12px rgba(6, 11, 47, 0.4);
	}
	.delete-image:hover {
		cursor: pointer;
	}
</style>
<div class="section">
	<div class="gallery-panel">
		<div class="text-right">
			<form action="" enctype="multipart/form-data" id="upload-form">
				<input type="hidden" name="action" value="upload_image">
				<input type="file" id="image-files" name="files[]" multiple accept=".png, .jpg, .jpeg, .gif" style="display:none;" />
			</form>
			<button class="btn btn-success btn-sm" onclick="upload_image()"><?php _e('Upload Image') ?></button>
		</div>
	</div>
	<?php
	if (!file_exists('../files')) {
			mkdir('../files', 0755, true);
		}
		if (!file_exists('../files/images')) {
			mkdir('../files/images', 0755, true);
		}
		if(file_exists('../files/images')){
			$images = [];
			$files = scandir( ABSPATH . 'files/images' );
			$files = array_diff($files, array('.', '..'));
			$index = 0;
			foreach ($files as $file) {
				$file_data = getimagesize('../files/images/'.$file);
				if(is_array($file_data)){
					$is_image = false;
					switch($file_data['mime']){
						case 'image/png':
							$is_image = true;
							break;
						case 'image/jpg':
							$is_image = true;
							break;
						case 'image/jpeg':
							$is_image = true;
							break;
						case 'image/gif':
							$is_image = true;
							break;
					}
					if($is_image){
						$images[] = array(
							'name'	=> $file,
							'width'	=> $file_data[0],
							'height'=> $file_data[1],
							'mime'=> $file_data['mime'],
							'url'	=> DOMAIN.'files/images/'.$file,
							'size'	=> round(filesize('../files/images/'.$file)/1000),
							'date' 	=> date("F d Y H:i:s", filemtime('../files/images/'.$file))
						);
					}
				}
			}
			if(count($images)){
				// Image exist
				usort($images, function ($b, $a) {
					return strtotime($a['date']) - strtotime($b['date']);
				});
				echo '<div class="image-gallery-plugin">';
				$total = count($images);
				$show = 24;
				$start = ($cur_page-1)*$show;
				$end = $start+$show;
				$total_page = ceil($total / $show);
				if($end > $total){
					$end = $total;
				}
				for($i=$start; $i<$end; $i++){
					$image = $images[$i];
					?>
					<div class="image-gallery-item">
						<div class="delete-image" data-name="<?php echo $image['name'] ?>">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</div>
						<a href="<?php echo $image['url'] ?>" target="_blank">
							<div class="thumb-container">
								<img class="item-thumbnail" src="<?php echo $image['url'] ?>">
							</div>
						</a>
						<div class="item-info">
							<div class="item-name"><?php echo $image['name'] ?></div>
							<div class="item-dim"><?php echo $image['width'].'x'.$image['height'] ?></div>
							<div class="item-size"><?php echo $image['size'] ?> kb</div>
						</div>
					</div>
					<?php
				}
				echo '</div>';
				echo '<div class="mb-4"></div>';
				echo '<p>'. _e('%a images in total.', $total) .'</p>';
				?>
				<div class="pagination-wrapper">
					<nav aria-label="Page navigation">
						<ul class="pagination pg-blue justify-content-center">
							<?php
							if($total_page){
								for($i = 0; $i<$total_page; $i++){
									$disabled = '';
									if($cur_page){
										if($cur_page == ($i+1)){
											$disabled = 'active disabled';
										}
									}
									echo '<li class="page-item '.$disabled.'"><a class="page-link" href="dashboard.php?viewpage=plugin&name=gallery&page='.($i+1).'">'.($i+1).'</a></li>';
								}
							}
							?>
						</ul>
					</nav>
				</div>
				<?php
			} else {
				_e('Empty');
			}
		} else {
			show_alert('Failed to create folder', 'warning');
		}
	?>
</div>
<script type="text/javascript">
	function upload_image() {
		document.getElementById("image-files").click();
	};
	document.getElementById("image-files").onchange = function() {
		let fd = new FormData();
		fd.append('action', 'upload_image');
		
		// Loop through all the selected files
		let files = $('#image-files')[0].files;
		for (let i = 0; i < files.length; i++) {
			let file = files[i];
			let fileExtension = file.name.split('.').pop().toLowerCase();
			
			// Validate the file extension
			if(fileExtension === 'png' || fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'gif') {
				fd.append('files[]', file);
			} else {
				alert('Invalid file format. Allowed formats are: .png, .jpg, .jpeg, .gif');
				return;
			}
		}
		
		$.ajax({
			url: 'includes/ajax-actions.php',
			type: 'POST',
			data: fd,
			contentType: false,
			processData: false,
			success: function(response) {
				let data = JSON.parse(response);
				if (data.result) {
					window.location = 'dashboard.php?viewpage=plugin&name=gallery&status=uploaded';
				} else {
					console.log(response);
					alert('Failed to upload. Check console');
				}
			},
		});
	};
	$(document).ready(function(){
		$('.delete-image').on('click', function(){
			let name = $(this).data('name');
			if(confirm('Delete this image?')){
				$.ajax({
					url: 'includes/ajax-actions.php',
					type: 'POST',
					data: {action: 'delete_image', name: name},
					success: function(response){
						if(response == 'ok'){
							window.location = 'dashboard.php?viewpage=plugin&name=gallery&status=deleted<?php echo ($cur_page > 1) ? '&page='.$cur_page : '' ?>';
						} else {
							console.log(response);
							alert('Failed to delete image. Check console')
						}
					},
				});
			}
		});
	});
</script>