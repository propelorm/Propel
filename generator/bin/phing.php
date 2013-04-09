#!/usr/bin/env php
<?php

/**
 * This is the Phing command line launcher. It starts up the system environment
 * tests for all important paths and properties and kicks of the main command-
 * line entry point of phing located in phing.Phing
 *
 * @version $Revision: 552 $
 */

// Set any INI options for PHP
// ---------------------------

$dirname = dirname(__FILE__);
$autoloaded = false;
foreach (array($dirname . '/../../', $dirname . '/../../../../../') as $dir) {
    if (file_exists($file = realpath($dir) . '/vendor/autoload.php')) {
        set_include_path($dir . '/vendor/phing/phing/classes' . PATH_SEPARATOR . get_include_path());
        include_once $file;

        $autoloaded = true;
        break;
    }
}

/* set classpath */
if (getenv('PHP_CLASSPATH')) {
    if (!defined('PHP_CLASSPATH')) {
        define('PHP_CLASSPATH', getenv('PHP_CLASSPATH') . PATH_SEPARATOR . get_include_path());
    }
    ini_set('include_path', PHP_CLASSPATH);
} else {
    if (!defined('PHP_CLASSPATH')) {
        define('PHP_CLASSPATH', get_include_path());
    }
}

require_once 'phing/Phing.php';

try {
    /* Setup Phing environment */
    Phing::startup();

    // Set phing.home property to the value from environment
    // (this may be NULL, but that's not a big problem.)
    Phing::setProperty('phing.home', getenv('PHING_HOME'));

    // Grab and clean up the CLI arguments
    $args = isset($argv) ? $argv : $_SERVER['argv']; // $_SERVER['argv'] seems to not work (sometimes?) when argv is registered
    array_shift($args); // 1st arg is script name, so drop it

    // Invoke the commandline entry point
    Phing::fire($args);

    // Invoke any shutdown routines.
    Phing::shutdown();
} catch (ConfigurationException $x) {
    Phing::printMessage($x);
    exit(-1); // This was convention previously for configuration errors.
} catch (Exception $x) {
    // Assume the message was already printed as part of the build and
    // exit with non-0 error code.
    exit(1);
}
