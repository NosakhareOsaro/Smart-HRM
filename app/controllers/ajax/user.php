<?php
$response = [
	'status' => false,
	'message' =>  'Unknown request.',
	'data' => null
];

if (isset($_POST['action']) && $_POST['action'] == 'DISSABLE_USER')
{
	if (csrf_verify('post') === true) {
	
		$user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$user 		=  new User;
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		}else {
				$is_active = 0;
			$update_data = [
				'is_active' => $is_active
			];
			
			if ($user->update('id', $user_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'user is now active distributed!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}

if (isset($_POST['action']) && $_POST['action'] == 'ACTIVATE_USER')
{
	if (csrf_verify('post') === true) {
	
		$user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$user 		=  new User;
		
		if (isset($project_data['is_admin'])){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => null ];
		}else {
				$is_active = 1;
			$update_data = [
				'is_active' => $is_active
			];
			
			if ($user->update('id', $user_id, $update_data)) {
				$response = [ 'status' => true, 'message' =>  'user is now active distributed!', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => null ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}


if (isset($_POST['action']) && $_POST['action'] == 'DELETE_USER')
{
	if (csrf_verify('post') === true) {
	
		$user_id		= (isset($_POST['user_id'])) ? trim($_POST['user_id']) : null;
		$user 		=  new user;
		$user_data 	= $user->get($user_id);
		
		if ($user_data==false){
			$response = [ 'status' => false, 'message' =>  'Page expired! Please reload.', 'data' => $user_data ];
		}else {
			if ($user->delete($user_id)) {
				$response = [ 'status' => true, 'message' =>  'User deleted.', 'data' => null ];
			} else {
				$response = [ 'status' => false, 'message' =>  'Something went wrong, please try again or reload page.', 'data' => $user ];
			}
		}
	} else {
		$response = [
		'status' => false,
		'message' =>  'Request expired, please try again or reload page.',
		'data' => null
	];
	}

}


echo json_encode($response);