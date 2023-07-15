<?php
require_once "../vendor/autoload.php";
require_once "../app/config/config.php";

		
define("DIR_BASE_PATH", dirname( __FILE__ )); 

$router = new AltoRouter();

$router->setBasePath('/smart_hrm'); // Comment out or remove this line before uploading to a live or production server/cpanel

$router->map('GET', '/?', function() use ($config) {
    require __DIR__ . '/views/home.php';
});
$router->map('GET', '/home/?', function() use ($config) {
    require __DIR__ . '/views/home.php';
});
$router->map('GET', '/about/?', function() use ($config) {
    require __DIR__ . '/views/about.php';
});
$router->map('GET', '/contacts/?', function() use ($config) {
    require __DIR__ . '/views/contacts.php';
});

$router->map('GET', '/core-values/?', function() use ($config) {
    require __DIR__ . '/views/core-values.php';
});
$router->map('GET', '/projects-carousel/?', function() use ($config) {
    require __DIR__ . '/views/projects-carousel.php';
});

$router->map('GET', '/projects/?', function() use ($config) {
    require __DIR__ . '/views/projects-grid.php';
});

$router->map('GET', '/services/?', function() use ($config) {
    require __DIR__ . '/views/services.php';
});
$router->map('GET', '/request-quote/?', function() use ($config) {
    require __DIR__ . '/views/request-quote.php';
});
$router->map('GET', '/about/?', function() use ($config) {
    require __DIR__ . '/views/about.php';
});
$router->map('GET', '/about/?', function() use ($config) {
    require __DIR__ . '/views/about.php';
});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
	echo "404 Not Found";
}