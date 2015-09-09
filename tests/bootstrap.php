<?php
/**
 * autoload class
 *
 * @author   Guillaume Tissier
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     https://github.com/guillaumetissier/ImageResizer
 */

spl_autoload_register(function($className) {

    $classFiles = explode('\\', $className);
    if ('ImageResizer' === $classFiles[0]) {
        $file = '../src/' . $classFiles[1] . '.php';
        if (file_exists($file)) {
            require_once($file);
        }
    }
});
