<?php

if (file_exists($file = dirname(__FILE__) . '/../vendor/.composer/autoload.php')) {
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../vendor/phing/phing/classes');

    require_once $file;
}
