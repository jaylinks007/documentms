<?php

// The single entry point for the entire application.

session_start();

// Require the autoloader. This will handle loading all namespaced classes.
require_once __DIR__ . '/../app/Core/autoloader.php';

/**
 * A simple helper function to parse the request URI.
 * @return string
 */
function get_uri()
{
    return trim(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
        '/'
    );
}

// Load the routes and instantiate the router.
$router = App\Core\Router::load(__DIR__ . '/../routes/web.php');

// Direct the request to the appropriate handler.
try {
    // The router will find the correct controller and method,
    // and that method will echo the rendered view.
    $router->direct(get_uri(), $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    // A simple error handler. In a real app, this would be more robust.
    http_response_code(500);
    // Never show detailed error messages in production.
    // For development, this is fine.
    echo 'An error occurred: ' . $e->getMessage();
}
