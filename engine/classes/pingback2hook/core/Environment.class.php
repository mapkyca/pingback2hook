<?php

/**
 * @file
 * 
 * Environment functions.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    /**
     * Logging
     */
    class Environment {

        /**
         * Retrieve wwwroot from configuration.
         * 
         * @param array $replacements Accepts arguments in the format of parse_url, allowing you to easily replace parts of the url e.g. 'schema' from http to https
         * @return string
         */
        public static function getWebRoot(array $replacements = null) {
            if (!$replacements)
                return \pingback2hook\System::$config->wwwroot;
            
            $url = parse_url(\pingback2hook\System::$config->wwwroot);

            // perform any replacements
            foreach ($replacements as $key => $value)
                $url[$key] = $value;

            return Page::buildUrl($url);
        }

        /**
         * Attempt to retrieve the system temporary directory using a variety of methods.
         * 
         * Use this function rather than sys_get_temp_dir() as this is only available in 
         * the latest versions of php (>= 5.2.3).
         */
        public static function getTempDir() {
            if (function_exists('sys_get_temp_dir'))
                return realpath(sys_get_temp_dir()) . '/';

            if ($temp = getenv('TMP'))
                return $temp . '/';

            if ($temp = getenv('TEMP'))
                return $temp . '/';

            if ($temp = getenv('TMPDIR'))
                return $temp . '/';

            // Last ditch
            $temp = tempnam(__FILE__, '');

            if (file_exists($temp)) {
                unlink($temp);
                return dirname($temp) . '/';
            }

            return false;
        }

    }

}