<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );
require_once( '../../../includes/commons.php' );
require_once( '../../../admin/admin-functions.php' );

if(!has_admin_access()){
	exit;
}

// Define the path to the videos directory
$video_directory = realpath('../../../files/videos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Get the action and video_name parameters securely
	$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
	// Ensure the action is 'check_video' and a valid video_name is provided
	if ($action === 'check_video') {
		$video_name = filter_input(INPUT_POST, 'video_name', FILTER_SANITIZE_SPECIAL_CHARS);
		if($video_name){
			// Ensure the video name contains only safe characters (e.g., letters, numbers, dashes)
			if (preg_match('/^[a-zA-Z0-9_-]+$/', $video_name)) {
				$video_file = $video_directory . '/' . $video_name . '.mp4';

				// Check if the file exists in the expected directory
				if (file_exists($video_file)) {
					// Respond with JSON indicating the video exists
					header('Content-Type: application/json');
					echo json_encode(['status' => 'exist']);
				} else {
					// Respond with JSON indicating the video does not exist
					header('Content-Type: application/json');
					echo json_encode(['status' => 'not-exist']);
				}
			} else {
				// Invalid video_name format
				header('Content-Type: application/json');
				echo json_encode(['status' => 'invalid-name']);
			}
		} else {
			// Invalid action or missing video_name
			header('Content-Type: application/json');
			echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
		}   
	} else if ($action === 'rename_video') {
		$new_name = filter_input(INPUT_POST, 'new_name', FILTER_SANITIZE_SPECIAL_CHARS);
		$old_name = filter_input(INPUT_POST, 'old_name', FILTER_SANITIZE_SPECIAL_CHARS);

		// Ensure new and old names are provided
		if ($new_name && $old_name) {
			$old_file = $video_directory . '/' . $old_name . '.mp4';
			$new_file = $video_directory . '/' . $new_name . '.mp4';

			// Check if the old file exists
			if (file_exists($old_file)) {
				// Check if the new file name does not already exist
				if (!file_exists($new_file)) {
					// Rename the file
					if (rename($old_file, $new_file)) {
						header('Content-Type: application/json');
						echo json_encode(['status' => 'success', 'new_name' => $new_name]);
					} else {
						header('Content-Type: application/json');
						echo json_encode(['status' => 'error', 'message' => 'Rename failed']);
					}
				} else {
					header('Content-Type: application/json');
					echo json_encode(['status' => 'error', 'message' => 'New file name already exists']);
				}
			} else {
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => 'Old file not found']);
			}
		} else {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
		}
	}
	// Delete video action
	else if ($action === 'delete_video') {
		$video_name = filter_input(INPUT_POST, 'video_name', FILTER_SANITIZE_SPECIAL_CHARS);

		if ($video_name) {
			$video_file = $video_directory . '/' . $video_name . '.mp4';

			// Check if the file exists
			if (file_exists($video_file)) {
				// Delete the file
				if (unlink($video_file)) {
					header('Content-Type: application/json');
					echo json_encode(['status' => 'success']);
				} else {
					header('Content-Type: application/json');
					echo json_encode(['status' => 'error', 'message' => 'Failed to delete the file']);
				}
			} else {
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => 'File not found']);
			}
		} else {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'error', 'message' => 'Invalid video name']);
		}
	}
	// Upload video
	else if ($action === 'upload_video') {
		if (isset($_FILES['videoUpload'])) {
			// Set the max file size (in bytes) - 2MB
			$max_file_size = 2 * 1024 * 1024; // 2MB in bytes

			// Allowed file types
			$allowed_mime_types = ['video/mp4'];
			// Prepare session message
			$type = 'success'; // Bootstrap alert class
			$message = '';

			$video_file = $_FILES['videoUpload'];

			// Check for any upload errors
			if ($video_file['error'] !== UPLOAD_ERR_OK) {
				$type = 'danger';
				$message = 'Upload error: ' . $video_file['error'];
				handleRedirect($type, $message);
			}

			// Check the file size
			if ($video_file['size'] > $max_file_size) {
				$type = 'danger';
				$message = 'File size exceeds the maximum allowed size of 2MB.';
				handleRedirect($type, $message);
			}

			// Check the MIME type of the file (ensure it's an actual MP4 video)
			$file_mime_type = mime_content_type($video_file['tmp_name']);
			if (!in_array($file_mime_type, $allowed_mime_types)) {
				$type = 'danger';
				$message = 'Invalid file type. Only MP4 videos are allowed.';
				handleRedirect($type, $message);
			}

			// Secure the file name by removing special characters
			$file_name = basename($video_file['name']);
			$safe_file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME)) . '.mp4';

			// Define the target file path
			$target_file_path = $video_directory . DIRECTORY_SEPARATOR . $safe_file_name;

			// Check if the file with the same sanitized name already exists
			if (file_exists($target_file_path)) {
				$type = 'danger';
				$message = 'A video with the name "' . htmlspecialchars($safe_file_name) . '" already exists. Please rename your file or upload a different one.';
				handleRedirect($type, $message);
			}

			// Move the uploaded file to the target directory
			if (move_uploaded_file($video_file['tmp_name'], $target_file_path)) {
				$message = 'Video uploaded successfully!';
			} else {
				$type = 'danger';
				$message = 'Error moving the uploaded file.';
			}

			// Redirect if the `redirect` parameter is set
			handleRedirect($type, $message);
		}
	}
} else {
	// Handle requests that aren't POST
	header('Content-Type: application/json');
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Helper function for handling redirection with message
function handleRedirect($type, $message)
{
	session_start(); // Start session to store the message
	$_SESSION['message'] = [
		'type' => $type,
		'text' => $message
	];
	if (isset($_POST['redirect'])) {
		header('Location: ' . filter_var($_POST['redirect'], FILTER_SANITIZE_URL));
		exit();
	}
}

?>
