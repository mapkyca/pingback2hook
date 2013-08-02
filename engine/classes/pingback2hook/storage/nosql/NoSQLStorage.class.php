<?php
/**
 * @file
 * NoSQL Storage class
 * 
 * @package storage\nosql
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\storage\nosql {
    
    /**
     * Definitions for NoSQL storage engines. 
     * 
     * Since the requirements for Home.API are currently very basic, much of the 
     * advanced functionality of these storage engines has been hidden.
     */
    abstract class NoSQLStorage {
        
        private static $engine;
        
        /**
         * Create new database.
         */
        abstract public function newDatabase($db);
        
        /**
         * Store some data.
         */
        abstract public function store($uuid, $data, array $params = null);
        
        /**
         * Delete an object.
         */
        abstract public function delete($uuid, array $params = null);
        
        /**
         * Retrieve an object.
         */
        abstract public function retrieve($uuid, array $params = null);
     
        
        /**
         * Generate a UUID from a class and a given name.
         * Use so that plugins can be sure that they're storing data within their own namespaces.
         * @param type $class A class, typically $this, when called within plugins
         * @param type $name The name of the thing you're storing
         */
        public static function generateUUID($class, $name) {

            $classname = preg_replace("/[^a-zA-Z0-9\s]/", "", get_class($class));
            $name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name);

            return "{$classname}-{$name}";
        }

        
        /**
         * Return the current nosql storage engine
         * 
         * @throws SubsystemFactoryException if no template is defined.
         * @return NoSQLStorage
         */
        public static function &getInstance() {
            if (!self::$engine)
                self::setInstance(\pingback2hook\core\SubsystemFactory::factory('nosqlstorage'));

            return self::$engine;
        }

        /**
         * Set the default nosql storage engine
         * @param NoSQLStorage $engine 
         */
        public static function setInstance(NoSQLStorage $engine) {
            self::$engine = $engine;
        }
    }
}