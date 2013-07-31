<?php

/**
 * @file
 * Main configuration file, put global stuff here, but most things should go in a settings.YOUR.HOST.NAME.php file for host specific configuration.
 * 
 * Available settings
 * ==================
 * 
 * - $CONFIG->wwwroot (url) : Override the detected base site root (handy for those building in localhost subdirs)
 * - $CONFIG->temp (filepath) : Override temp directory settings (recommended you leave this as default unless you're having trouble on your system)
 * - $CONFIG->docroot (filepath) : Physical location of schedulables files (in 99.999% of the time it is best to leave this auto detected)
 * - $CONFIG->site_secret (string) : Site secret key used for various things, including form token generation. It is safe, and recommended, to leave this auto generated
 * - $CONFIG->couchdburl (url) : Couch DB storage engine connection string, e.g. http://localhost:5984/
 * - $CONFIG->couchdb (string) : Couch DB (defaults to pingback)
 * - $CONFIG->debug (bool) : Debug on/off
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */
/**
 * Create the CONFIG object.
 * You will want to do this at the top of settings file.
 */
global $CONFIG;
if (!isset($CONFIG))
    $CONFIG = new stdClass;

/** Next, custom configuration will be included */
