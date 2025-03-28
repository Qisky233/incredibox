<?php

require('../../../config.php');
require('../../../init.php');

if(!USER_ADMIN){
	die('error');
}

$errors = array();

if(isset($_POST['action']) && isset($_POST['id'])){
	if($_POST['action'] === 'get_user'){
		echo json_encode(User::getById((int)$_POST['id']));
	} elseif($_POST['action'] === 'update'){
		if(!check_errors()){
			$user = User::getById((int)$_POST['id']);
			if($user){
				$user->username = $_POST['username'];
				$user->email = $_POST['email'];
				$user->bio = $_POST['bio'];
				$user->xp = (int)$_POST['xp'];
				if($_POST['password'] && $_POST['password'] != ''){
					$user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				}
				$user->update();
				$user->update_xp();
				echo 'ok';
			}
		} else {
			echo json_encode($errors);
		}
	} elseif($_POST['action'] === 'update_role'){
		if(USER_ADMIN){
			$user = User::getById((int)$_POST['id']);
			if($user && isset($_POST['role'])){
				if($user->updateRole($_POST['role'])){
					echo 'ok';
				}
			}
		} else {
			echo 'Forbidden!';
		}
	}
} elseif(isset($_GET['action']) && isset($_GET['id'])){
	if($_GET['action'] === 'delete'){
		$user = User::getById((int)$_GET['id']);
		if($user){
			$user->delete();
		}
		if(isset($_GET['redirect'])){
			header('Location: '.$_GET['redirect'].'&name=users&status=deleted');
		}
	}
}

function check_errors(){
	global $errors;
	$val = 0;
	$_POST['username'] = $_POST['username'];
	$username = esc_string($_POST['username']);
	if(!isset($_POST['password'])){
		$_POST['password'] = '';
	}
	$password = str_replace(' ','',$_POST['password']);

	$this_user = User::getById((int)$_POST['id']);
	if($this_user->username != $_POST['username']){
		$other_user = User::getByUsername($_POST['username']);
		if($other_user && $other_user->id != (int)$_POST['id']){
			$errors[] = 'User "'.$_POST['username'].'" already exist!';
			$val = 1;
		}
	}
		
	if($username != $_POST['username']){
		$errors[] = 'Username contains illegal characters!';
		$val = 1;
	}
	if($_POST['email']){
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Email not valid!';
			$val = 1;
		} else {
			$email_user = User::getByEmail($_POST['email']);
			if($email_user && $email_user->id != (int)$_POST['id']){
				$errors[] = 'Email "'.$_POST['email'].'" already exist!';
				$val = 1;
			}
		}
	}
	if ($password != $_POST['password']) {
		$errors[] = 'Password must not contain any "space"!';
		$val = 1;
	}
	if(!$val){
		if(file_exists(ABSPATH.'includes/banned-username.json')){
			$usernames = json_decode(file_get_contents(ABSPATH.'includes/banned-username.json'), true);
			foreach ($usernames as $name) {
				if($username === $name){
					$errors[] = 'Username "'.$_POST['username'].'" is not available!';
					return 1;
				}
			}
		}
		if(file_exists(ABSPATH.'includes/banned-words.json')){
			$words = json_decode(file_get_contents(ABSPATH.'includes/banned-words.json'), true);
			foreach ($words as $word) {
				if(strpos('-'.$username, $word)){
					$errors[] = 'Username contains banned word!';
					return 1;
				}
			}
		}
	}
	return $val;
}

?>