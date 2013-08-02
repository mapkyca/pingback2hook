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
    use pingback2hook\xml\XmlParser as XmlParser;
    use pingback2hook\core\Input as Input;
    use pingback2hook\endpoints\Endpoint as Endpoint;
    use pingback2hook\templates\Template as Template;
    
    class Pingback extends Mention {
        
        public function init() {
            
            Page::create('pingback', function($page, $subpages){
                
                if ($endpoint = Endpoint::get($subpages[0])) {
                
                    if ($xml = XmlParser::unserialise(Input::getPOST()))
                    {
                        // Get source and target url
                        
                        
                    
                        
                        
                        // Do we have a source and target URL?
                        if ($source_url && $target_url) {

                            // Check we haven't already got this one registered.
                            if (self::isTargetRegistered($source_url, $target_url))
                                throw new AlreadyRegisteredException("Target $target_url has already been registered.");
                        
                            // Check whether target is in source url
                            if (!$details = self::checkSourceUrl($source_url, $target_url))
                                throw new NoLinkFoundException("$target_url not found in $source_url");
                        
                            // Append configuration details
                            $details['endpoint'] = $subpages[0];
                 
                            if (self::saveMention($target_url, $source_url, $details)) {

                                header('HTTP/1.1 202 Accepted');
                                
                                
                                
                                
                                
                                
                                
                                
                                // Accept ping back
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            }
                            else
                                throw new TargetNotSupportedException("Problem saving mention to $target_url from $source_url.");
                        
                        }
                        else
                            throw new SourceNotFoundException('Source and target variables missing.');
                    }
                    else
                        throw new TargetNotSupportedException('Problem parsing XML Pingback.');
                }
                else
                    throw new TargetNotFoundException('No endpoint of that definition specified.');
                
            });
        }
    }
}