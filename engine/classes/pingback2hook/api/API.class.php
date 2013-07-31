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

namespace pingback2hook\api {
    
    use pingback2hook\core\Page as Page;
    
    class API {
        
        
        public function init() {
            
            Page::create('api', function($page, $subpages){
                
            });
        }
    }
}