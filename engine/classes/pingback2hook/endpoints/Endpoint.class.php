<?php

/**
 * @file
 * 
 * Endpoint definitions.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\endpoints {

    use pingback2hook\core\Log as Log;
    use pingback2hook\core\Page as Page;
    use pingback2hook\i18n\i18n as i18n;
    use pingback2hook\core\Input as Input;
    use pingback2hook\templates\Template as Template;
    use pingback2hook\plugins\Plugin as Plugin;

    class Endpoint {

        /// API Definition
        private static $endpoints;
        /// Base path
        private static $basepath;
        
        public static function get($endpoint) {
            if (isset(self::$endpoints[$endpoint]))
                return self::$endpoints[$endpoint];
            
            return false;
        }

        /**
         * Initialise definition directory.
         * @param type $definitionDirectory
         */
        public static function init($definitionDirectory) {

            self::$basepath = $definitionDirectory;
            self::$endpoints = array();

            // Load all init files
            if ($handle = opendir(self::$basepath)) {
                while ($api_def = readdir($handle)) {
                    
                    // must be directory and not begin with a .
                    if ((substr($api_def, 0, 1) !== '.') && (!is_dir(self::$basepath . $api_def)) && (strpos($api_def, '.ini') !== false)) {
                        
                        self::$endpoints = array_merge(self::$endpoints, parse_ini_file(self::$basepath . $api_def, true));
                        
                    }
                }
            }
            
        }

    }

}
