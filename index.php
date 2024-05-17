<?php

if (PHP_VERSION_ID < 8.2) {
    echo 'Composer 2.3.0 dropped support for autoloading on PHP < 8.2 and you are running ' . PHP_VERSION . ', please upgrade PHP or use Composer 2.2 LTS via "composer self-update --2.2". Aborting.' . PHP_EOL;
    exit(1);
}

require_once 'autoloader/YshLaoader.php';


return YshLaoader::loader();