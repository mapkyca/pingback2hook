<?php

/**
 * @file
 * Subsystem Factory
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {


    /**
     * Subsystem Factory.
     * This class lets you take full advantage of the on the fly loading capabilities
     * of PHP, letting you define constructors for certain Subsystems, passing parameters
     * and thus deferring instantiation until the appropriate getInstance method is called.
     * 
     * The upshot of this means that potentially intensive subsystems (templating, i18n, database etc) can
     * have their loading deferred until they are actually needed. Meaning it is safe to blanket include
     * the engine's boot file in things that, say, only act as simple json endpoints.
     */
    class SubsystemFactory {

        /// Registry of constructors
        private static $constructorRegistry = array();

        /**
         * Register a class constructor
         * @param type $label Label of the subsystem, e.g. 'template' or 'database'
         * @param type $class Handler class, new class will be instantiated as new $class(...)
         * @param array $parameters Any parameters to pass to the class constructor.
         */
        public static function registerConstructor($label, $class, array $parameters = null) {
            $subsystem = array();
            $subsystem['class'] = $class;
            if ($parameters)
                $subsystem['parameters'] = $parameters;

            self::$constructorRegistry[$label] = $subsystem;
        }

        /**
         * Return an instance of the subsystem Subsystem
         * @param type $label Label as passed to SubsystemFactory::registerConstructor()
         */
        public static function factory($label) {
            $subsystem = self::$constructorRegistry[$label];
            if (!$subsystem)
                throw new \pingback2hook\core\exceptions\SubsystemFactoryException("Subsystem Factory does not know how to make '$label'");

            $reflect = new \ReflectionClass($subsystem['class']);
            if (isset($subsystem['parameters']))
                return \pingback2hook\core\Events::trigger("factory:$label", 'construct', array('return' => $reflect->newInstanceArgs($subsystem['parameters'])));

            return \pingback2hook\core\Events::trigger("factory:$label", 'construct', array('return' => $reflect->newInstance()));
        }

    }

}