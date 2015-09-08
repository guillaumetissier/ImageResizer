<?php
spl_autoload_register(function($className) {

    $dir = '../src/';
    $classFiles = explode('\\', $className);

    $classFile = array_pop($classFiles) . '.php';
    if (file_exists($dir . $classFile)) {
        require_once($dir . $classFile);
        return;
    }

    $dir       = '../vendor/mikey179/vfsStream/src/main/php/';
    $classFile = str_replace('\\', '/', $className) . '.php';
    if (file_exists($dir . $classFile)) {
        require_once($dir . $classFile);
        return;
    }
});
