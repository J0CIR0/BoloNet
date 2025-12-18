<?php
spl_autoload_register(function ($class_name) {
    $directories = [
        'controllers/',
        'models/',
        'config/'
    ];
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    foreach ($directories as $directory) {
        $file = $directory . ucfirst($class_name) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    $full_path = __DIR__ . '/' . $class_name . '.php';
    if (file_exists($full_path)) {
        require_once $full_path;
        return;
    }
    throw new Exception("No se pudo cargar la clase: $class_name");
});
?>