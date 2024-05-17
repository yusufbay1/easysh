<?php
namespace Autoloader;
class AutoLoader
{
    public static function register($directory)
    {
        spl_autoload_register(function ($className) use ($directory) {
            $classFile = $directory . '/' . basename(str_replace('\\', '/', $className)) . '.php';
            if (file_exists($classFile)) {
                require_once $classFile;
                return true;
            }
            return false;
        });
    }
}