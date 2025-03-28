<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );

if(ADMIN_DEMO || !USER_ADMIN){
	return;
}

if(isset($_POST['action'])){
	if($_POST['action'] == 'delete'){
		$conn = open_connection();
		$st = $conn->prepare("SELECT id FROM tags WHERE name = :name LIMIT 1");
		$st->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		if($row){
			// Tag name is exist in the database
			// Delete tag_links related rows
			$tag_id = $row['id'];
			$conn = open_connection();
			$st = $conn->prepare("DELETE FROM tag_links WHERE tag_id = :tag_id");
			$st->bindValue(":tag_id", $tag_id, PDO::PARAM_STR);
			$st->execute();
			//
			$st = $conn->prepare("DELETE FROM tags WHERE id = :id LIMIT 1");
			$st->bindValue(":id", $tag_id, PDO::PARAM_STR);
			$st->execute();
			echo 'deleted';
		} else {
			echo 'tag not exist';
		}
	} else if($_POST['action'] == 'update-tag-usage'){
		$conn = open_connection();
		$tags = get_tags('usage', 1000);
		if(!empty($tags)){
			foreach ($tags as $tag_name) {
				$st = $conn->prepare("SELECT id FROM tags WHERE name = :name LIMIT 1");
				$st->bindValue(":name", $tag_name, PDO::PARAM_STR);
				$st->execute();
				$tag_id = $st->fetch(PDO::FETCH_ASSOC)['id'];
				
				// Count the number of tag links
				$st = $conn->prepare("SELECT COUNT(*) FROM tag_links WHERE tag_id = :tag_id");
				$st->bindValue(":tag_id", $tag_id, PDO::PARAM_INT);
				$st->execute();
				$count = $st->fetchColumn();
				
				// Update the tag usage count
				$st = $conn->prepare("UPDATE tags SET usage_count = :usage_count WHERE id = :tag_id");
				$st->bindValue(":tag_id", $tag_id, PDO::PARAM_INT);
				$st->bindValue(":usage_count", $count, PDO::PARAM_INT);
				$st->execute();
			}
			echo 'ok';
		}
	}
}

?>