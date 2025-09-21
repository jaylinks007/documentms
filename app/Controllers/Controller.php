<?php

namespace App\Controllers;

/**
 * Base Controller
 *
 * This class provides common functionality for all other controllers.
 */
class Controller
{
    /**
     * Render a view file.
     *
     * This method loads a view file and passes data to it.
     * It uses output buffering to capture the rendered HTML and return it as a string.
     *
     * @param string $view The name of the view file (e.g., 'auth.login').
     * @param array  $data An associative array of data to make available to the view.
     * @return string The rendered HTML.
     * @throws \Exception If the view file is not found.
     */
    public function view($view, $data = [])
    {
        // Convert dot notation to a file path
        $viewPath = str_replace('.', '/', $view);
        $filePath = __DIR__ . "/../../app/Views/{$viewPath}.php";

        if (!file_exists($filePath)) {
            throw new \Exception("View file not found: {$filePath}");
        }

        // Extract the data array into individual variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        require $filePath;

        // Get the contents of the buffer and clean it
        $content = ob_get_clean();

        return $content;
    }
}
