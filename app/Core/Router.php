<?php

namespace App\Core;

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
            return $this->callAction(
                ...explode('@', $this->routes[$requestType][$uri])
            );
        }

        http_response_code(404);
        // This path is relative to the new file location
        require __DIR__ . '/../Views/errors/404.php';
        exit();
    }

    protected function callAction($controller, $action)
    {
        // Prepend the full namespace to the controller name.
        $controller = "App\\Controllers\\{$controller}";

        // The autoloader will handle requiring the file.
        if (!class_exists($controller)) {
            throw new \Exception("Controller class not found: {$controller}");
        }

        $controllerInstance = new $controller;

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception(
                "{$controller} does not respond to the {$action} action."
            );
        }

        return $controllerInstance->$action();
    }
}
