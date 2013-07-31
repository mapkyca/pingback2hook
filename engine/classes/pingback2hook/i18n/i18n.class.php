<?php

/**
 * @file
 * Site internationalisation engine.
 * 
 * This provides a very simple internationalisation engine.
 * 
 * @package i18n
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\i18n {

    /**
     * Internationalisation root class.
     */
    abstract class i18n {

        protected static $i18n;

        abstract public function write($key, array $parameters = null, $language = null);

        abstract public function translationExists($key, $language = null);

        /**
         * Remove special characters and return a transliterated version of text in URL compatible format
         * 
         * TODO: Find a better place
         */
        public static function transliterate($string) {
            
            if (!is_callable('iconv'))
                throw new \pingback2hook\core\exceptions\i18nException(i18n::w('i18n:exception:function_iconv_missing'));
            
            $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string); // Attempt the transliteration of non-ascii chars

            $string = preg_replace("/[^\w ]/", "", $string);

            $string = str_replace(" ", "-", $string);
            $string = str_replace("--", "-", $string);
            $string = str_replace("/", "-", $string);
            $string = trim($string);
            $string = strtolower($string);

            return $string;
        }

        /**
         * Alias for i18n::w()
         */
        public static function w($key, array $parameters = null, $language = null) {
            return static::getInstance()->write($key, $parameters, $language);
        }

        /**
         * Return the current template engine.
         * 
         * @throws SubsystemFactoryException if no template is defined.
         * @return i18n
         */
        public static function &getInstance() {
            if (!self::$i18n) {
                self::setInstance(\pingback2hook\core\SubsystemFactory::factory('i18n'));
            }

            return self::$i18n;
        }

        /**
         * Set the default i18n engine.
         * @param i18n $i18n 
         */
        public static function setInstance(i18n $i18n) {
            self::$i18n = $i18n;
        }

    }

}