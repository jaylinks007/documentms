<?php

/**
 * A basic PSR-4 autoloader.
 *
 * This function is registered with spl_autoload_register to automatically
 * load classes based on their namespace and class name. It assumes the
 * "App" namespace maps to the "app" directory.
 */
spl_autoload_register(function ($class) {
    // The namespace prefix we are looking for.
    $prefix = 'App\\';

    // The base directory for the namespace prefix.
    // __DIR__ is the 'core' directory, so we go up one level.
    $base_dir = __DIR__ . '/../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader.
        return;
    }

    // Get the relative class name.
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, and append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it.
    if (file_exists($file)) {
        require $file;
    }
});
