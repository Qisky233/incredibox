<?php

require_once( TEMPLATE_PATH . '/functions.php' );

if(PRETTY_URL){
	if(count($url_params) > 3 || count($url_params) < 2){
		// Category page only contains 3 parameter max,
		// If more than that or less than 2, the url is not valid
		// Show 404 screen
		require( ABSPATH . 'includes/page-404.php' );
		return;
	}
	if(isset($url_params[2]) && !is_numeric($url_params[2])){
		// Page number should be a number
		// Show 404 screen
		require( ABSPATH . 'includes/page-404.php' );
		return;
	}
}

$cur_page = 1;
if(isset($url_params[2])){
	$_GET['page'] = $url_params[2];
	if(!is_numeric($_GET['page'])){
		$_GET['page'] = 1;
	}
}
if(isset($_GET['page'])){
	$cur_page = htmlspecialchars($_GET['page']);
	if(!is_numeric($cur_page)){
		$cur_page = 1;
	}
}

if(get_setting_value('allow_slug_translation')){
	$_original_slug = get_translation_key($_GET['slug']);
	if($_original_slug && substr($_original_slug, 0, 5) == 'slug:'){
		// The slug have a translation
		$_GET['slug'] = str_replace('slug:', '', $_original_slug); // The slug variable is modified
	}
}

$category = Category::getBySlug($_GET['slug']);

if($category){
	if($lang_code != 'en'){
		// If use translation (localization)
		// Begin translate the content if has translation
		$translated_fields = get_content_translation('category', $category->id, $lang_code, 'all');
		if(!is_null($translated_fields)){
			$category->name = isset($translated_fields['name']) ? $translated_fields['name'] : $category->name;
			$category->description = isset($translated_fields['description']) ? $translated_fields['description'] : $category->description;
			$category->meta_description = isset($translated_fields['meta_description']) ? $translated_fields['meta_description'] : $category->meta_description;
		}
	}

	$items_per_page = get_setting_value('category_results_per_page');
	$data = get_game_list_category_id($category->id, $items_per_page, $items_per_page*($cur_page-1));
	$games = $data['results'];
	$total_games = $data['totalRows'];
	$total_page = $data['totalPages'];
	if($cur_page > $total_page){
		// Page number is more than actual maximum page
		require( ABSPATH . 'includes/page-404.php' );
		return;
	}
	if(isset($category->meta_description) && $category->meta_description != ''){
		$meta_description = $category->meta_description;
	} else {
		$meta_description = _t('Play %a Games', $category->name).' | '.SITE_DESCRIPTION;
	}
	$archive_title = _t($category->name);
	$page_title = _t('%a Games', $category->name).' | '.SITE_DESCRIPTION;
	if(file_exists(TEMPLATE_PATH . '/category.php')){
		// category.php is preferred over archive.php 
		require( TEMPLATE_PATH . '/category.php' );
	} else {
		// For backward compatibility
		require( TEMPLATE_PATH . '/archive.php' );
	}
} else {
	require( ABSPATH . 'includes/page-404.php' );
}

?>