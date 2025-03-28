<?php

ini_set('max_execution_time', 60000);

require( '../../../config.php' );
require( '../../../init.php' );
include_once '../../../includes/plugin.php';

if(!$login_user || !USER_ADMIN || !PRETTY_URL){
	exit('forbidden');
}

if(!file_exists('../../../index_static.php') || !file_exists('../../../static/')){
	exit('not active');
}

$list_urls = [];

if(true){
	$list_files[] = '';

	if (true){
		//games
		if(true){
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
					$list_urls[] = str_replace(DOMAIN, '', $url);
				}
			}
		}

		//categories
		if(true){
			$cats = get_all_categories();
			foreach ($cats as $cat) {
				if (strpos($cat->slug, '&') == false) {
					$url = get_permalink('category', $cat->slug);
					$list_urls[] = str_replace(DOMAIN, '', $url);
				}
			}
			//generate category pagination
			foreach ($cats as $cat) {
				if (strpos($cat->slug, '&') == false) {
					$list = Category::getListByCategory($cat->id, 36, 0);
					$total_pages = (int)$list['totalPages'];
					for($i=0; $i<$total_pages; $i++){
						$url = get_permalink('category', $cat->slug, array('page' => ($i+1)));
						$list_urls[] = str_replace(DOMAIN, '', $url);
					}
				}
			}
		}

		//pages
		if(true){
			$pages = Page::getList()['results'];
			foreach ($pages as $item) {
				if (strpos($item->slug, '&') == false) {
					$url = get_permalink('page', $item->slug);
					$list_urls[] = str_replace(DOMAIN, '', $url);
				}
			}
		}

		//posts
		if(true){
			if(file_exists('../../../'.PLUGIN_PATH.'posts/Post.php')){
				include_once '../../../'.PLUGIN_PATH.'posts/Post.php';
			}
			if(defined('POST_ACTIVE')){
				$list = Post::getList(6);
				$posts = $list['results'];
				if($posts){
					//add post page
					$url = get_permalink('post');
					$list_urls[] = str_replace(DOMAIN, '', $url);
					foreach ($posts as $post) {
						if (strpos($post->slug, '&') == false) {
							$url = get_permalink('post', $post->slug);
							$list_urls[] = str_replace(DOMAIN, '', $url);
						}
					}
					// generate post pagination
					$total_pages = (int)$list['totalPages'];
					for($i=0; $i<$total_pages; $i++){
						$url = get_permalink('post', ($i+1));
						$list_urls[] = str_replace(DOMAIN, '', $url);
					}
				}
			}
		}
	}
}

echo json_encode($list_urls);

?>