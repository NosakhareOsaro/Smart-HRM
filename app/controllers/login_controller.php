<?php
$activePage = 'login';

// Redirect if already logged in
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    $user = new User();
    $activeUserData = $user->get(trim($_SESSION['user_id']));
    
    if ($activeUserData['role'] === 'admin') {
        header('Location: ' . base_url("/admin-dashboard"));
    } else {
        header('Location: ' . base_url("/employee-dashboard"));
    }
    exit;
}

$has_form_error = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify('post') === true) {
    // Get the form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    // Perform form validation
    if (empty($email) || empty($password)) {
        alert_flash('Email and password are required.', 'error');
        $has_form_error = true;
    } else {
        // Perform login authentication
        $user = new User();
        $logged_in_user = $user->getByEmail($email);

        if ($logged_in_user !== false && is_array($logged_in_user)) {
            if (password_verify($password, $logged_in_user['password'])) {
                $_SESSION['is_login'] = true;
                $_SESSION['user_id'] = $logged_in_user['id'];

                if ($logged_in_user['role'] === 'admin') {
                    header('Location: ' . base_url("/admin-dashboard"));
                } else {
                    header('Location: ' . base_url("/employee-dashboard"));
                }
                exit;
            } else {
                alert_flash('Invalid email or password.', 'error');
                $has_form_error = true;
            }
        } else {
            alert_flash('Invalid email or password.', 'error');
            $has_form_error = true;
        }
    }
}
?>
