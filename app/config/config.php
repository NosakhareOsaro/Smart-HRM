<?php

$config = [
    'app_name' => 'Smart HRM',
    'base_url' => '/smart_hrm',
    'database_host' => '127.0.0.1',
    'database_user' => 'root',
    'database_password' => '',
    'database_name' => 'db_smart_hrm',
    'smtp_host' => '', // server53.web-hosting.com
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_secure' => '', // ssl
    'smtp_port' => '', // 465
    'default_email' => '',
    'default_name' => '',
    'upload_path' => __DIR__ . '/../uploads/',
    'item_per_page' => '',
    'encryption_key' => '',
    'encryption_cipher' => '',
    'paystack_test_mode' => true,
    'paystack_live_secret_key' => '',
    'paystack_live_public_key' => '',
    'paystack_test_secret_key' => '',
    'paystack_test_public_key' => '',
];

session_start();

// Database configuration
define('DB_HOST', $config['database_host']);
define('DB_USER', $config['database_user']);
define('DB_PASSWORD', $config['database_password']);
define('DB_NAME', $config['database_name']);

// Application configuration
define('APP_NAME', $config['app_name']);
define('APP_URL', $config['base_url']);
define('APP_DEBUG', true);

// Email configuration
define('SMTP_HOST', $config['smtp_host']);
define('SMTP_PORT', $config['smtp_port']);
define('SMTP_USERNAME', $config['smtp_username']);
define('SMTP_PASSWORD', $config['smtp_password']);
define('MAIL_FROM_ADDRESS', $config['default_email']);
define('MAIL_FROM_NAME', $config['default_name']);

// File upload configuration
define('UPLOAD_PATH', $config['upload_path']);

// Pagination configuration
define('ITEMS_PER_PAGE', $config['item_per_page']);

// Encryption configuration
define('ENCRYPTION_KEY', $config['encryption_key']);
define('ENCRYPTION_CIPHER', $config['encryption_cipher']);

// Timezone configuration
date_default_timezone_set('Africa/Lagos');

// Error reporting configuration
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ...
// Additional configuration settings
// ...

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Helpers.php';
