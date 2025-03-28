<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );
require_once( '../../../includes/commons.php' );
require_once( '../../../admin/admin-functions.php' );

if(is_login() && USER_ADMIN && !ADMIN_DEMO){
	if(isset($_POST['action']) && ($_POST['action'] == 'import' || $_POST['action'] == 'generate-small')){
		$id = (int)$_POST['id'];
		$game = Game::getById($id);
		if($game){
			if($_POST['action'] == 'import' && substr($game->thumb_1, 0, 4) == 'http'){
				if( IMPORT_THUMB ){
					// Check if webp is activated
					$use_webp = get_setting_value('webp_thumbnail');
					import_thumbnail($game->thumb_2, $game->slug, 2);
					$extension = pathinfo($game->thumb_2, PATHINFO_EXTENSION);
					$game->thumb_2 = '/thumbs/'.$game->slug.'_2.'.$extension;
					if($use_webp){
						$file_extension = pathinfo($game->thumb_2, PATHINFO_EXTENSION);
						$game->thumb_2 = str_replace('.'.$file_extension, '.webp', $game->thumb_2);
					}
					//
					import_thumbnail($game->thumb_1, $game->slug, 1);
					$extension = pathinfo($game->thumb_2, PATHINFO_EXTENSION);
					$game->thumb_1 = '/thumbs/'.$game->slug.'_1.'.$extension;
					if($use_webp){
						$file_extension = pathinfo($game->thumb_1, PATHINFO_EXTENSION);
						$game->thumb_1 = str_replace('.'.$file_extension, '.webp', $game->thumb_1);
					}
					if( SMALL_THUMB ){
						$output = pathinfo($game->thumb_2);
						$game->thumb_small = '/thumbs/'.$game->slug.'_small.'.$output['extension'];
						if($use_webp){
							$file_extension = pathinfo($game->thumb_2, PATHINFO_EXTENSION);
							$game->thumb_small = str_replace('.'.$file_extension, '.webp', $game->thumb_small);
							generate_small_thumbnail($game->thumb_2, $game->slug);
						} else {
							generate_small_thumbnail($game->thumb_2, $game->slug);
						}
					}
					$game->update();
					echo 'ok';
				}
			} elseif($_POST['action'] == 'generate-small'){
				if( SMALL_THUMB ){
					generate_small_thumbnail($game->thumb_2, $game->slug);
					if(true){
						$output = pathinfo($game->thumb_2);
						if($game->source == 'self'){
							$game->thumb_small = '/games/'.$game->slug.'_small.'.$output['extension'];
						} else {
							$game->thumb_small = '/thumbs/'.$game->slug.'_small.'.$output['extension'];
						}
						$game->update();
					} else {
						echo 'error';
					}
				}
				echo 'ok';
			} else {
				echo substr($game->thumb_1, 0, 4);
			}
		} else {
			var_dump($game);
		}
	} else {
		echo('a');
	}
} else {
	echo 'x';
}

?>