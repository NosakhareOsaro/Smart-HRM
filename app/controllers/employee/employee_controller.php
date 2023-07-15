<?php

$activePage = 'employees';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . base_url('/logout'));
    exit;
}

$userID = trim($_SESSION['user_id']);
$user = new User();
$activeUserData = $user->get($userID);


// Verify user data and role
if ($activeUserData === false || !is_array($activeUserData) || $activeUserData['role'] !== 'admin') {
    header('Location: ' . base_url('/logout'));
    exit;
}

$users = $user->getAll();

// Proceed with the admin dashboard code here
// ...

$has_form_error = false;

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (csrf_verify('post') === true) ){
   
	// Get the form data
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : null;
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;
    $repeatPassword = isset($_POST['repeat_password']) ? trim($_POST['repeat_password']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $role = isset($_POST['role']) ? trim($_POST['role']) : null;
    $employeeId = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : null;


	// Create a new instance of the User class
	$user = new User();

    // Perform form validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($repeatPassword) || empty($phone) || empty($role) || empty($employeeId)) {
        // Handle form validation errors
        // Redirect back to the registration form with an error message if needed
        // Example: header("Location: registration-form.php?error=Please fill in all fields");
		alert_flash ('All fields are required.', 'error');
		$has_form_error = true;
    }
	elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Handle invalid email error
		alert_flash ('Invalid email address. Please enter a valid email address.', 'error');
		$has_form_error = true;
    }
	elseif ( $user->getByEmail($email) != false) {
		alert_flash ('The email address has already been used. Please enter a different email address.', 'error');
		$has_form_error = true;
		
	}
	// Validate password length
    elseif (strlen($password) < 6) {
        // Handle password length error
		alert_flash ('Password must be at least 6 characters.', 'error');
		$has_form_error = true;
    }

    elseif ($password !== $repeatPassword) {
        // Handle password mismatch error
        // Redirect back to the registration form with an error message if needed
        // Example: header("Location: registration-form.php?error=Passwords do not match");
		alert_flash ('Password didnt match', 'error');
		$has_form_error = true;
    }else{

	

  
    // Perform user registration
    $result = $user->create($firstName, $lastName, $email, $password, $phone, $role, $employeeId);

    if ($result) {
        // Registration success
        // Redirect to a success page or login page
        // Example: header("Location: login.html");

		header('Location: ' . $_SERVER['HTTP_REFERER']);
        
    } else {
        // Registration failed
        // Handle registration failure, such as displaying an error message or redirecting back to the registration form with an error message
        // Example: header("Location: registration-form.php?error=Registration failed");
		alert_flash ('Unable to create user record', 'error');
		$has_form_error = true;

    }
}
} else {
    // Invalid request method, redirect to the registration form or display an error message
    // Example: header("Location: registration-form.php");
	//alert_flash ('Invalid request method', 'error');
	//$has_form_error = true;

}


	

