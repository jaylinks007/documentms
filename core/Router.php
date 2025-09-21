<?php

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];

    public static function load($file)
    {
        $router = new static;
        $router->routes = require $file;
        return $router;
    }

    public function direct($uri, $requestType)
    {
        if (array_key_exists($uri, $this->routes[$requestType])) {
            // In the next phase, this will call a controller.
            // e.g., return $this->callAction(...explode('@', $this->routes[$requestType][$uri]));

            // For now, just return a placeholder.
            return "Route found for URI: {$uri}";
        }

        // A simple 404 handler.
        http_response_code(404);
        echo "404 Not Found";
        exit();
    }
}
