<?php

ini_set('max_execution_time', 180000);

require( '../../../config.php' );
require( '../../../init.php' );
include_once '../../../includes/plugin.php';

$target_folder = '../../../static';

$list_files = [];

$update_mode = '';

$dont_rename = false;

if(isset($_POST['action'])){
	$update_mode = $_POST['action'];
} else {
	die();
}

if($update_mode == 'all'){
	if(!file_exists('../../../index_static.php')){
		copy('index_static.php', '../../../index_static.php');
	}
}

// Remove _index_static if exist to avoid failed to rename on end script
if(file_exists('../../../_index_static.php')){
	unlink('../../../_index_static.php');
}

// Rename index_static to _index_static
// To activate dynamic mode
if(file_exists('../../../index_static.php')){
	rename('../../../index_static.php', '../../../_index_static.php');
}

if(PRETTY_URL){
	$list_files[] = array(
		'url'	=> DOMAIN,
		'path'	=> $target_folder.'/'
	);

	if($update_mode == 'new_content'){
		$category_list = [];
		//Games
		$games = Game::getList(50)['results'];
		foreach ($games as $game) {
			$url = get_permalink('game', $game->slug);
			$path = $target_folder.str_replace(DOMAIN, '/', $url);
			if(file_exists($path.'index.html')){
				break;
			} else {
				$arr = commas_to_array($game->category);
				foreach($arr as $item){
					if(!in_array(strtolower($item), $category_list)){
						$category_list[] = strtolower($item);
					}
				}
				$list_files[] = array(
					'url'	=> $url,
					'path'	=> $path
				);
			}
		}
		//Update all related categories
		if(count($category_list)){
			$cats = [];
			foreach ($category_list as $item) {
				$category = Category::getByName($item);
				if($category){
					$cats[] = $category;
				}
			}
			if(count($cats)){
				foreach ($cats as $cat) {
					if (strpos($cat->slug, '&') == false) {
						$url = get_permalink('category', $cat->slug);
						$path = $target_folder.str_replace(DOMAIN, '/', $url);
						$list_files[] = array(
							'url'	=> $url,
							'path'	=> $path
						);
					}
				}
				//generate category pagination
				foreach ($cats as $cat) {
					if (strpos($cat->slug, '&') == false) {
						$list = Category::getListByCategory($cat->id, 36, 0);
						$total_pages = (int)$list['totalPages'];
						for($i=0; $i<$total_pages; $i++){
							$url = get_permalink('category', $cat->slug, array('page' => ($i+1)));
							$path = $target_folder.str_replace(DOMAIN, '/', $url);
							$list_files[] = array(
								'url'	=> $url,
								'path'	=> $path
							);
						}
					}
				}
			}
		}
		//Pages
		$pages = Page::getList()['results'];
		foreach ($pages as $item) {
			if (strpos($item->slug, '&') == false) {
				$url = get_permalink('page', $item->slug);
				$path = $target_folder.str_replace(DOMAIN, '/', $url);
				if(file_exists($path.'index.html')){
					break;
				} else {
					$list_files[] = array(
						'url'	=> $url,
						'path'	=> $path
					);
				}
			}
		}
		//Posts
		if(defined('POST_ACTIVE')){
			$posts = Post::getList()['results'];
			if($posts){
				foreach ($posts as $post) {
					if (strpos($post->slug, '&') == false) {
						$url = get_permalink('post', $post->slug);
						$path = $target_folder.str_replace(DOMAIN, '/', $url);
						if(file_exists($path.'index.html')){
							break;
						} else {
							$list_files[] = array(
								'url'	=> $url,
								'path'	=> $path
							);
						}
					}
				}
			}
		}
	} elseif ($update_mode == 'single_url'){
		if($login_user && USER_ADMIN){
			$url = str_replace(DOMAIN, '', $_POST['url']);
			$url = DOMAIN . $url;
			$path = $target_folder.str_replace(DOMAIN, '/', $url);
			$list_files[] = array(
				'url'	=> $url,
				'path'	=> $path
			);
		}
	} elseif ($login_user && USER_ADMIN){
		if($update_mode == 'all'){
			delete_files($target_folder);
			if(!file_exists($target_folder)){
				//unlink($target_folder);
				mkdir($target_folder, 0755, true);
			}
		}

		//games
		if($update_mode == 'all' || $update_mode == 'games'){
			$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT slug FROM games";
			$st = $conn->prepare($sql);
			$st->execute();
			$games = $st -> fetchAll();
			$conn = null;
			foreach ($games as $game) {
				if (strpos($game['slug'], '&') == false) {
					$url = get_permalink('game', $game['slug']);
					$path = $target_folder.str_replace(DOMAIN, '/', $url);
					$list_files[] = array(
						'url'	=> $url,
						'path'	=> $path
					);
				}
			}
		}

		//categories
		if($update_mode == 'all' || $update_mode == 'categories'){
			$cats = get_all_categories();
			foreach ($cats as $cat) {
				if (strpos($cat->slug, '&') == false) {
					$url = get_permalink('category', $cat->slug);
					$path = $target_folder.str_replace(DOMAIN, '/', $url);
					$list_files[] = array(
						'url'	=> $url,
						'path'	=> $path
					);
				}
			}
			//generate category pagination
			foreach ($cats as $cat) {
				if (strpos($cat->slug, '&') == false) {
					$list = Category::getListByCategory($cat->id, 36, 0);
					$total_pages = (int)$list['totalPages'];
					for($i=0; $i<$total_pages; $i++){
						$url = get_permalink('category', $cat->slug, array('page' => ($i+1)));
						$path = $target_folder.str_replace(DOMAIN, '/', $url);
						$list_files[] = array(
							'url'	=> $url,
							'path'	=> $path
						);
					}
				}
			}
		}

		//pages
		if($update_mode == 'all' || $update_mode == 'pages'){
			$pages = Page::getList()['results'];
			foreach ($pages as $item) {
				if (strpos($item->slug, '&') == false) {
					$url = get_permalink('page', $item->slug);
					$path = $target_folder.str_replace(DOMAIN, '/', $url);
					$list_files[] = array(
						'url'	=> $url,
						'path'	=> $path
					);
				}
			}
		}

		//posts
		if($update_mode == 'all' || $update_mode == 'posts'){
			if(file_exists('../../../'.PLUGIN_PATH.'posts/Post.php')){
				include_once '../../../'.PLUGIN_PATH.'posts/Post.php';
			}
			if(defined('POST_ACTIVE')){
				$list = Post::getList(6);
				$posts = $list['results'];
				if($posts){
					//add post page
					$url = get_permalink('post');
					$path = $target_folder.str_replace(DOMAIN, '/', $url);
					$list_files[] = array(
						'url'	=> $url,
						'path'	=> $path
					);
					foreach ($posts as $post) {
						if (strpos($post->slug, '&') == false) {
							$url = get_permalink('post', $post->slug);
							$path = $target_folder.str_replace(DOMAIN, '/', $url);
							$list_files[] = array(
								'url'	=> $url,
								'path'	=> $path
							);
						}
					}
					// generate post pagination
					$total_pages = (int)$list['totalPages'];
					for($i=0; $i<$total_pages; $i++){
						$url = get_permalink('post', ($i+1));
						$path = $target_folder.str_replace(DOMAIN, '/', $url);
						$list_files[] = array(
							'url'	=> $url,
							'path'	=> $path
						);
					}
				}
			}
		}
	}
}

foreach ($list_files as $item) {
	if (!file_exists($item['path'])) {
		mkdir($item['path'], 0755, true);
	}
	$ch = curl_init($item['url']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curl = curl_exec($ch);
	curl_close($ch);
	file_put_contents($item['path'].'index.html', $curl);
}

rename('../../../_index_static.php', '../../../index_static.php');

echo 'ok';

?>