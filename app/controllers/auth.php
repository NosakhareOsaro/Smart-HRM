<?php
$user_authicated = false;

if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
	
	// check if the user exist
	$user_id = (isset($_SESSION['user_id'])) ? trim($_SESSION['user_id']) : null; 
	$user =  new User;
	$active_user_data = $user->get($user_id);
	if ($active_user_data !== false && is_array($active_user_data) && $active_user_data['is_active'] !== false) {
		// There is a logged in user, do nothing just proceed to loading the page.
	$user_authicated= true;
	
	} 
} 

if ($user_authicated === false) {
	header('location: ' . base_url('login') );
	exit;
}

	