<?php
// Include the autoloader and configuration files
require_once "vendor/autoload.php";
require_once "app/config/config.php";

// Define the base path
define("DIR_BASE_PATH", dirname(__FILE__));

// Create a new instance of AltoRouter
$router = new AltoRouter();

// Set the base path (Remove this line before deploying to a live or production server)
$router->setBasePath('/smart_hrm');

// Define the routes and their corresponding controllers and views

$router->map('GET|POST', '/register/?', function() use ($config) {
    require __DIR__ . '/app/controllers/register_controller.php';
    require __DIR__ . '/resources/views/auth/register.php';
});


$router->map('GET|POST', '/login/?', function() use ($config) {
    require __DIR__ . '/app/controllers/login_controller.php';
    require __DIR__ . '/resources/views/auth/login.php';
});

$router->map('GET|POST', '/admin-dashboard/?', function() use ($config) {
    require __DIR__ . '/app/controllers/admin_dashboard_controller.php';
    require __DIR__ . '/resources/views/dashboard/admin_dashboard.php';
});

$router->map('GET|POST', '/employee-dashboard/?', function() use ($config) {
    require __DIR__ . '/app/controllers/employee_dashboard_controller.php';
    require __DIR__ . '/resources/views/dashboard/employee_dashboard.php';
});

$router->map('GET|POST', '/employees/?', function() use ($config) {
    require __DIR__ . '/app/controllers/employee/employee_controller.php';
    require __DIR__ . '/resources/views/employee/employees.php';
});

$router->map('GET|POST', '/attendance/?', function() use ($config) {
    require __DIR__ . '/app/controllers/employee/attendance_controller.php';
    require __DIR__ . '/resources/views/employee/attendance.php';
});

$router->map('GET|POST', '/salary/?', function() use ($config) {
    require __DIR__ . '/app/controllers/employee/salary_controller.php';
    require __DIR__ . '/resources/views/employee/salary.php';
});

$router->map('GET', '/logout/?', function() use ($config) {
    require __DIR__ . '/app/controllers/logout.php';
});

// Match the current request URL with the defined routes
$match = $router->match();

// Execute the corresponding closure or return a 404 error if the route is not found
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // No matching route was found
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
