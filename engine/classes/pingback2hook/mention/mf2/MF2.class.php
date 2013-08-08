<?php

/**
 * @file
 * Funky microformat2 support.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\mention\mf2 {
    
    use mf2\Parser as Parser;
    use pingback2hook\core\Events as Events;
 
    class MF2 {
    
        
        
        public static function init() {
            
            // Listen to the mention creation events so we can parse for extra data
            Events::register('mention', 'parsesource', function($namespace, $event, $parameters){
                
                
                // TODO: Parse source for mf2 data.
                
                
            });
        }
    }
    
}