<?php

/**
 * @file
 * 
 * Mention services.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\webhooks {
    
    use pingback2hook\core\Events as Events;
    
    class Webhook {
        
        
        public static function init() {
            
            // Listen for save events in order to trigger webhooks
            Events::register('mention', 'save', function($namespace, $event, &$parameters) {
                
                
                
                // TODO: Send webhook
                
                
                
                
            });
        }
    }
}