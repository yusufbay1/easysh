<?php

use Autoloader\AutoLoader;

require_once 'autoloader/AutoLoader.php';

class YshLaoader
{
    public static function loader(): void
    {
        self::allow();
        Autoloader::register('helpers');
        AutoLoader::register('app/Libraries');
        AutoLoader::register('app/Controllers');
        AutoLoader::register('app/Models');
        require_once 'routes/route/route.php';
        require_once 'routes/api.php';
        require_once 'routes/web.php';
    }

    private static function allow()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header('Access-Control-Allow-Methods: *');
    }
}