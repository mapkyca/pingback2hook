<?php

/**
 * @file
 * Input handling functions.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    /**
     * Inputs
     */
    class Input {

        /// Cached and sanitised input variables
        protected static $sanitised_input = array();

        /**
         * Retrieve input.
         *
         * @param string $variable The variable to retrieve.
         * @param mixed $default Optional default value.
         * @param callable $filter_hook Optional hook for input filtering, takes one parameter and returns the filtered version. eg function($var){return htmlentities($var);}
         * @return mixed
         */
        public static function get($variable, $default = null, $filter_hook = null) {
            // Has input been set already
            if (isset(self::$sanitised_input[$variable])) {
                $var = self::$sanitised_input[$variable];

                return $var;
            }

            if (isset($_REQUEST[$variable])) {
                if (is_array($_REQUEST[$variable]))
                    $var = $_REQUEST[$variable];
                else
                    $var = trim($_REQUEST[$variable]);

                if (is_callable($filter_hook))
                    $var = $filter_hook($var);

                return $var;
            }

            return $default;
        }

        /**
         * Set an input value
         *
         * @param string $variable The name of the variable
         * @param mixed $value its value
         */
        public static function set($variable, $value) {
            if (!isset(self::$sanitised_input))
                self::$sanitised_input = array();

            if (is_array($value)) {
                foreach ($value as $key => $val)
                    $value[$key] = trim($val);

                self::$sanitised_input[trim($variable)] = $value;
            }
            else
                self::$sanitised_input[trim($variable)] = trim($value);
        }

        /**
         * Get raw POST request data.
         * @param callable $filter_hook Optional hook for input filtering, takes one parameter and returns the filtered version. eg function($var){return htmlentities($var);}
         * @return string|false
         */
        public static function getPOST($filter_hook = null) {
            global $GLOBALS;

            $post = '';

            if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
                $post = $GLOBALS['HTTP_RAW_POST_DATA'];

            // If always_populate_raw_post_data is switched off, attempt another method.
            if (!$post)
                $post = file_get_contents('php://input');

            // If we have some results then return them
            if ($post) {

                if ((isset($filter_hook)) && (is_callable($filter_hook)))
                    $post = $filter_hook($post);

                return $post;
            }

            return false;
        }

    }

}