<?php

$activePage = 'admin_dashboard';

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

