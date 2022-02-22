<?php

use Doctrine\Common\ClassLoader;
use my\package\Class_Name as ClassName;
use my\package_name\Class_Name as ClassName2;

spl_autoload_register(function ($class) {
    $file = sprintf("src/%s.php", str_replace("\\", DIRECTORY_SEPARATOR, $class));
    $file = dirname($file) . DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, basename($file));
    if (file_exists($file)) {
        require $file;
    }
});