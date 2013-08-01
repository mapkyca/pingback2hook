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
    use pingback2hook\core\Input as Input;
    use pingback2hook\endpoints\Endpoint as Endpoint;
    
    class Webmention extends Mention {
        
        public function init() {
            
            Page::create('webmention', function($page, $subpages){
                
                if ($endpoint = Endpoint::get($subpages[0])) {
                 
                    $source_url = Input::getPOST('source');
                    $target_url = Input::getPOST('target');

                    // Do we have a source and target URL?
                    if ($source_url && $target_url) {

                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    }
                    else
                        throw new SourceNotFoundException('Source and target variables missing.');
                    
                }
                else
                    throw new TargetNotFoundException('No endpoint of that definition specified.');
                
            });
        }
    }
}