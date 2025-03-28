<?php
/**
 * This file is responsible for displaying and managing the plugin's admin interface.
 * It allows administrators to view and update plugin settings, such as saving and
 * loading preferences via AJAX.
 */

// Dynamically set the plugin slug by getting the plugin folder name
$plugin_slug = basename(dirname(__FILE__)); // 'sample-plugin'

if(!has_admin_access()){
	exit;
}

// Handle form submission based on 'action'
if (isset($_POST['action'])) {
	if ($_POST['action'] == 'thumbnail_selector') {
		// Save string preference
		$thumbnail_selector = $_POST['thumbnail_selector'] ?? '';
		set_plugin_pref($plugin_slug, 'thumbnail_selector', $thumbnail_selector);
		show_alert('String preference saved!', 'success');
	}
	
	if ($_POST['action'] == 'enable_debug') {
		// Save boolean preference
		$enable_debug = isset($_POST['enable_debug']) ? 'true' : 'false';
		set_plugin_pref($plugin_slug, 'enable_debug', $enable_debug);
		show_alert('Boolean preference saved!', 'success');
	}
}

$cur_page = 'config';

if(isset($_GET['slug'])){
	if($_GET['slug'] == 'videos'){
		$cur_page = 'videos';
	}
}

if(isset($_SESSION['message'])){
	if(isset($_SESSION['message']['text'])){
		$type = 'success';
		if($_SESSION['message']['type'] === 'error' || $_SESSION['message']['type'] === 'danger'){
			$type = 'danger';
		}
		show_alert($_SESSION['message']['text'], $type);
	}
	unset($_SESSION['message']);
}

?>

<div class="section section-full">
	<ul class="nav nav-tabs custom-tab" role="tablist">
		<li class="nav-item" role="presentation">
			<a href="dashboard.php?viewpage=plugin&name=video-hover-play" class="nav-link <?php echo ($cur_page == 'config') ? 'active' : '' ?>"><?php _e('Configuration') ?></a>
		</li>
		<li class="nav-item" role="presentation">
			<a href="dashboard.php?viewpage=plugin&name=video-hover-play&slug=videos" class="nav-link <?php echo ($cur_page == 'videos') ? 'active' : '' ?>" ><?php _e('Videos') ?></a>
		</li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<?php if($cur_page == 'config'){ ?>
		<div class="tab-pane tab-container active" id="vhp-config">
			<div class="general-wrapper">
				<div class="bs-callout bs-callout-info">
					This plugin allows users to automatically play a game video preview when hovering over a game thumbnail, similar to popular platforms like Poki.com and CrazyGames.com.
				</div>

				<h4>Video Hover Play Configuration</h4>
				<hr>

				<!-- String Preference -->
				<div class="mb-3">
					<p><b>Game Thumbnail Selector</b></p>
					<form method="post">
						<input type="hidden" name="action" value="thumbnail_selector">
						<?php $thumbnail_selector = get_plugin_pref($plugin_slug, 'thumbnail_selector', 'a > .list-game > .list-thumbnail'); ?>
						<input type="text" class="form-control" name="thumbnail_selector" placeholder="a > .list-game > .list-thumbnail" value="<?php echo htmlspecialchars($thumbnail_selector); ?>" autocomplete="off" required>
						<button class="btn btn-primary btn-md mt-2">Save</button>
					</form>
				</div>
				<hr>

				<!-- Boolean Preference -->
				<div class="mb-3">
					<p><b>Enable Debugging</b></p>
					<form method="post">
						<input type="hidden" name="action" value="enable_debug">
						<?php $enable_debug = get_plugin_pref_bool($plugin_slug, 'enable_debug', false); ?>
						<div class="form-check">
							<input type="checkbox" name="enable_debug" class="form-check-input" id="enable_debug" <?php echo $enable_debug ? 'checked' : ''; ?>>
							<label class="form-check-label" for="enable_debug">Enable</label>
						</div>
						<button class="btn btn-primary btn-md mt-2">Save</button>
					</form>
				</div>
			</div>
		</div>
		<?php } else if($cur_page == 'videos'){
			if(!file_exists('../files/videos')){
				mkdir('../files/videos', 0755, true);
			}
			?>
		<div class="tab-pane tab-container active" id="vhp-videos">
			<div class="general-wrapper">
				<div class="bs-callout bs-callout-info">
					The video file name should match the slug name of your target game.
				</div>
				<div class="bs-callout bs-callout-info">
					To ensure optimal display, make sure the video dimensions are neither too small nor too large compared to the thumbnail. If the thumbnail size is 300x300 pixels, the video should ideally be between 250 and 400 pixels for better visual quality and efficient loading.
				</div>
				<!-- Video Upload Form -->
				<div class="upload-wrapper mb-4">
					<form method="POST" action="../content/plugins/video-hover-play/ajax-handler.php" enctype="multipart/form-data">
						<input type="hidden" name="action" value="upload_video">
						<input type="hidden" name="redirect" value="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=video-hover-play&slug=videos">
						<div class="mb-3">
							<label for="videoUpload" class="form-label">Upload Video (MP4 Only, Max: 2MB)</label>
							<input class="form-control" type="file" id="videoUpload" name="videoUpload" accept=".mp4" required>
						</div>
						<div class="alert alert-info" role="alert">
							<strong>Note:</strong> Only MP4 files are allowed. Maximum file size is 2MB.
						</div>
						<button type="submit" class="btn btn-primary" onclick="return validateUpload()">Upload</button>
					</form>
				</div>
				<hr>
				<!-- PHP Logic to List Video Files with Pagination -->
				<?php if (file_exists('../files/videos')): ?>
					<?php
					$files = scan_files('files/videos');
					$video_files = [];
					$videos_per_page = 20; // Define how many videos to display per page
					foreach ($files as $file) {
						if (pathinfo($file, PATHINFO_EXTENSION) == 'mp4') {
							$video_files[] = [
								'name' => basename($file),
								'path' => "../$file",
								'date' => filemtime('../'.$file), // Get creation/upload time
							];
						}
					}
					// Sort video files by creation/upload date (latest first)
					usort($video_files, function($a, $b) {
						return $b['date'] - $a['date'];
					});
					// Calculate total pages and current page
					$total_videos = count($video_files);
					$total_pages = ceil($total_videos / $videos_per_page);
					$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
					$offset = ($current_page - 1) * $videos_per_page;
					$paginated_videos = array_slice($video_files, $offset, $videos_per_page);
					// Function to format file size
					function format_file_size($size) {
						$units = array('B', 'KB', 'MB', 'GB');
						$power = $size > 0 ? floor(log($size, 1024)) : 0;
						return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
					}
					// Function to format date
					function format_date($timestamp) {
						return date("F d, Y H:i:s", $timestamp);
					}
					// Function to get video dimensions using ffmpeg
					function get_video_dimensions($file_path) {
						// Check if exec is disabled
						if (!function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
							return 'Unknown';
						}
						
						$ffmpeg = '/usr/bin/ffmpeg';
						$command = "$ffmpeg -i $file_path 2>&1";
						$output = shell_exec($command);
						$matches = [];
						if (preg_match('/, (\d+x\d+),/', $output, $matches)) {
							return $matches[1]; // Return resolution like 1920x1080
						}
						return 'Unknown';
					}
					if (!empty($video_files)): ?>
						<table class="table table-striped table-hover mt-4">
							<thead>
								<tr>
									<th scope="col">Video File</th>
									<th scope="col">Size</th>
									<th scope="col">Resolution</th>
									<th scope="col">Uploaded/Created Date</th>
									<th scope="col">Actions</th>
								</tr>
							</thead>
							<tbody id="video-list-body">
								<?php foreach ($paginated_videos as $video): ?>
									<?php
									$video_path = $video['path'];
									$file_size = filesize($video_path);
									$resolution = get_video_dimensions($video_path);
									$uploaded_date = format_date($video['date']);
									?>
									<tr>
										<td><?= $video['name'] ?></td>
										<td><?= format_file_size($file_size) ?></td>
										<td><?= $resolution ?></td>
										<td><?= $uploaded_date ?></td>
										<td>
											<!-- Rename Button (trigger modal) -->
											<button class="btn btn-primary btn-sm" 
													data-bs-toggle="modal" 
													data-bs-target="#rename-video-modal" 
													data-name="<?= pathinfo($video['name'], PATHINFO_FILENAME) ?>">
												Rename
											</button>
											<!-- Delete Button (trigger action) -->
											<button class="btn btn-danger btn-sm btn-delete-video" data-name="<?= pathinfo($video['name'], PATHINFO_FILENAME) ?>">Delete</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<p>
							Page <?php echo $current_page ?> of <?php echo $total_pages ?>. <?php echo $total_videos ?> videos in total.
						</p>
						<!-- Pagination Dropdown -->
						<div class="mb-3">
							<label for="paginationSelect" class="form-label">Select Page:</label>
							<select id="paginationSelect" class="form-select">
								<?php for ($i = 1; $i <= $total_pages; $i++): ?>
									<option value="<?= $i ?>" <?= $i == $current_page ? 'selected' : '' ?>>Page <?= $i ?></option>
								<?php endfor; ?>
							</select>
						</div>
					<?php else: ?>
						<div class="alert alert-warning">No video files found!</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="alert alert-danger">Video directory does not exist!</div>
				<?php endif; ?>
			</div>
		</div>
<?php } ?>
	</div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="rename-video-modal" tabindex="-1" aria-labelledby="rename-label-video-modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="rename-label-video-modal">Rename video: <?= $video['name'] ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="POST" action="rename_video.php">
					<div class="mb-3">
						<label for="current_video_name" class="form-label">New video name</label>
						<input type="text" class="form-control" id="current_video_name" name="new_video_name" autocomplete="off" required>
						<input type="hidden" name="old_video_name" value="<?= $video['name'] ?>">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Rename</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	// Validate the upload file
	function validateUpload() {
		const uploadInput = document.getElementById('videoUpload');
		const file = uploadInput.files[0];
		const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

		if (file && file.size > maxFileSize) {
			alert('The file size exceeds 2MB. Please upload a smaller file.');
			return false; // Prevent form submission
		}
		return true; // Allow form submission
	}
	document.querySelectorAll('[data-bs-target="#rename-video-modal"]').forEach(button => {
		button.addEventListener('click', function () {
			const fileNameWithoutExt = this.getAttribute('data-name');
			const inputField = document.querySelector('#current_video_name');
			const oldFileNameField = document.querySelector('input[name="old_video_name"]');
			
			if (inputField) {
				inputField.value = fileNameWithoutExt; // Set the default value
			}
			
			if (oldFileNameField) {
				oldFileNameField.value = fileNameWithoutExt; // Set the old video name (without extension)
			}
		});
	});
	document.querySelector('#rename-video-modal form').addEventListener('submit', function (e) {
		e.preventDefault(); // Prevent form submission

		const newVideoName = document.querySelector('#current_video_name').value;
		const oldVideoName = document.querySelector('input[name="old_video_name"]').value;

		// Check if the new name is different from the old name
		if (newVideoName === oldVideoName) {
			alert('The new video name must be different from the old one.');
			return; // Stop form submission
		}

		const formData = new FormData();
		formData.append('action', 'rename_video');
		formData.append('new_name', newVideoName);
		formData.append('old_name', oldVideoName);

		fetch('../content/plugins/video-hover-play/ajax-handler.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success') {
				// Update the UI to reflect the renamed video file
				document.querySelector(`button[data-name="${oldVideoName}"]`).setAttribute('data-name', newVideoName);
				document.querySelector(`button[data-name="${newVideoName}"]`).closest('tr').querySelector('td').innerText = newVideoName + '.mp4';

				alert('Video renamed successfully!');
				const modal = bootstrap.Modal.getInstance(document.querySelector('#rename-video-modal'));
				modal.hide(); // Hide the modal
			} else {
				alert(data.message || 'An error occurred.');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('An error occurred while renaming the video.');
		});
	});
	// Handle video deletion
	document.querySelectorAll('.btn-delete-video').forEach(button => {
		button.addEventListener('click', function () {
			const videoName = this.getAttribute('data-name');

			if (confirm('Are you sure you want to delete this video?')) {
				const formData = new FormData();
				formData.append('action', 'delete_video');
				formData.append('video_name', videoName);

				fetch('../content/plugins/video-hover-play/ajax-handler.php', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						// Remove the video row from the UI
						this.closest('tr').remove();
						alert('Video deleted successfully!');
					} else {
						alert(data.message || 'An error occurred.');
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('An error occurred while deleting the video.');
				});
			}
		});
	});
	// JavaScript to handle pagination using the select dropdown
	document.getElementById('paginationSelect').addEventListener('change', function() {
		const selectedPage = this.value;
		// Redirect to the selected page, keeping the current URL
		window.location.href = 'dashboard.php?viewpage=plugin&name=video-hover-play&slug=videos&page=' + selectedPage;
	});
</script>