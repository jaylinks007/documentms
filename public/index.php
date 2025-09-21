<?php

// The single entry point for the entire application.

session_start();

// For now, we will require files manually.
// In a more complex app, an autoloader (like Composer's) would be used.
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Router.php';

// A simple helper function to parse the request URI.
function get_uri() {
    return trim(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
        '/'
    );
}

// Load the routes and instantiate the router.
$router = Router::load(__DIR__ . '/../routes/web.php');

// Direct the request to the appropriate handler based on the URI and request method.
// In the next phase, this will call a controller method which will return a view.
try {
    echo $router->direct(get_uri(), $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    // Basic error handling
    http_response_code(500);
    echo 'An error occurred: ' . $e->getMessage();
}
