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

namespace pingback2hook\mention {
    
    use pingback2hook\core\Page as Page;
    
    class Pingback extends Mention {
        
        public function init() {
            
            Page::create('pingback', function($page, $subpages){
                
            });
        }
    }
}