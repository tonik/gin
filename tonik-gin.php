<?php

/*
Plugin Name: tonik/gin
Plugin URI: http://tonik.pl
Description: Foundation of the Tonik WordPress Starter Theme. Provides all custom functionalities which it offers.
Version: 3.1.0
Author: Tonik
Author URI: http://tonik.pl
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Domain Path: /resources/lang
Text Domain: tonik.gin
 */

spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'Tonik\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base
    // directory, replace namespace separators with directory
    // separators in the relative class name, append with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
