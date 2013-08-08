<?php

/**
 * @file
 * 
 * Startup file
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */
/**
 * \mainpage 
 * This is Pingback2Hook.
 */
// Library files to include in order
require_once(dirname(__FILE__) . "/version.php");
require_once(dirname(dirname(__FILE__)) . "/config/settings.php");

// Include any domain specific configuration
$settings_file = dirname(dirname(__FILE__)) . "/config/settings.{$_SERVER['SERVER_NAME']}.php"; 
if (file_exists($settings_file))
    require_once($settings_file);

// Register an autoloader
spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = dirname(__FILE__) . '/classes/' . $class . '.class.php';
    if (file_exists($file))
        include_once($file);
});

// Manually include some external stuff
require_once(dirname(__FILE__) . '/ext/mf2/mf2/Parser.php'); // Microformats library

// Initialise the site
global $CONFIG;
\pingback2hook\System::init($CONFIG);

