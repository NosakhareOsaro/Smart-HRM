<?php
$ajax_authenticated = false;

if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
	
	// check if the user exist
	$user_id = (isset($_SESSION['user_id'])) ? trim($_SESSION['user_id']) : null; 
	$user =  new User;
	$active_user_data = $user->get($user_id);
	if ($active_user_data !== false && is_array($active_user_data)) {
		// There is a logged in user, do nothing just proceed to with the requested operation.
		$ajax_authenticated = true;
	}
}
if ($ajax_authenticated === false) {
	$response = [
		'status' => false,
		'message' =>  'Unauthorized access, please try again or reload page.',
		'data' => null
	];
	echo json_encode($response);
	exit;
}