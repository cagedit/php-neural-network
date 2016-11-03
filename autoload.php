<?php
require_once(__DIR__ . '/helpers.php');
spl_autoload_register(function($class) {
    $class = str_replace('\\', '/', $class);
    $filepath = __DIR__ . "/{$class}.php";
    if (file_exists($filepath)) {
        require_once($filepath);
    }
});