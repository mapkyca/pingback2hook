<?php

/**
 * @file
 * Event handling.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    /**
     * Event handling
     */
    class Events {

        /// Events management
        protected static $events;
        
        /**
         * Register an event listener with the system.
         *
         * An event listener can be set to listen for a specific event, or a collection of events using wildcards.
         *
         * Namespaces are used to define the namespace of the event.
         * 
         * The function being registered must have the following prototype:
         *
         * \code
         * 	function Object::foo($namespace, $event, &$parameters)
         * 	{
         * 	    // Your code, return boolean false will stop any further events being processed
         * 	}
         * \endcode
         *
         * @section Example
         *
         * This example registers a couple of hooks for handling a number of different events:
         *
         * \code
         * 	// Listen to the saved event of a blog post (but not any of its subclasses)
         * 	Events::register('obj:blog', 'saved', 'object_save_event_handler');
         *
         * 	// Listen to the update event of all objects and subclasses
         * 	Events::register('obj:*', 'updated', 'object_update_event_handler');
         * \endcode
         *
         * @param string $namespace The event class, you may specify wild cards.
         * @param string $event The event, you may specify wild cards.
         * @param string $handler Handling function
         * @param int $priority Value determining the order of execution.
         */
        public static function register($namespace, $event, $handler, $priority = 500) {

            // Turn highchair/elgg style wildcards into correct ones.
            $namespace = str_replace('*', '.*', $namespace);
            $namespace = str_replace('/', '\/', $namespace);
            $namespace = '/^' . str_replace('all', '.*', $namespace) . '$/';

            $event = str_replace('*', '.*', $event);
            $event = str_replace('/', '\/', $event);
            $event = '/^' . str_replace('all', '.*', $event) . '$/';


            if (!isset(self::$events))
                self::$events = array();

            if (!isset(self::$events[$namespace]))
                self::$events[$namespace] = array();

            if (!isset(self::$events[$namespace][$event]))
                self::$events[$namespace][$event] = array();

            while (isset(self::$events[$namespace][$event][$priority]))
                $priority++;

            self::$events[$namespace][$event][$priority] = $handler;

            ksort(self::$events[$namespace][$event]);
            
            return true;
        }

        /**
         * Trigger an event.
         * 
         * If a triggered event returns false it will prevent subsequent events
         * in the chain from executing.
         *
         * @param string $namespace The event namespace
         * @param string $event The event
         * @param array $parameters Associated array of parameters.
         * @return bool
         */
        public static function trigger($namespace, $event, array $parameters = NULL) {           
            $merged = array();
            if (!$parameters)
                $parameters = array(
                    'halt' => false,
                    'return' => null
                );

            if (!self::$events)
                self::$events = array();

            // Get events we're triggering
            foreach (self::$events as $namespace_key => $event_list) {
                // Does the namespace being triggered match a registered namespace?
                if (preg_match($namespace_key, $namespace)) {
                    foreach (self::$events[$namespace_key] as $event_key => $function_list) {
                        // Does the event being triggered match a registered event
                        if (preg_match($event_key, $event)) {
                            // Now add and prioritise events
                            foreach ($function_list as $priority => $function) {
                                // Adjust priority to free slot
                                while (isset($merged[$priority]))
                                    $priority++;
                                $merged[$priority] = $function;
                            }
                        }
                    }
                }
            }

            // Now sort and execute 
            ksort($merged); 
            foreach ($merged as $function) {
                $result = null;
                
                $result = call_user_func_array($function, array($namespace, $event, $parameters));
                
                if (isset($result)) $parameters['return'] = $result;
                if ($parameters['halt'])
                    return $parameters['return'];
            }

            return $parameters['return'];
        }

        
        /**
         * Return whether a given event has a handler.
         * @param type $namespace
         * @param type $event
         * @return bool
         */
        public static function exists($namespace, $event) { 
            if (isset(self::$events[$namespace][$event]))
                return is_array(self::$events[$namespace][$event]);
            return false;
        }

    }

}