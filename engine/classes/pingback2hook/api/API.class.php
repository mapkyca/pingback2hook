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
    use pingback2hook\core\Log as Log;
    use pingback2hook\core\Input as Input;
    use pingback2hook\endpoints\Endpoint as Endpoint;
    use pingback2hook\mention\Mention as Mention;
    use pingback2hook\storage\nosql\NoSQLStorage as NoSQLStorage;
    use pingback2hook\storage\nosql\CouchDB as CouchDB;
    use pingback2hook\i18n\i18n as i18n;
    use pingback2hook\templates\Template as Template;
    
    class API {
                
        /** Retrieve the latest pingbacks for a url. */
        public function latest($target_url, $limit = 10, $offset = 0) {
            return Mention::mentionsOnTarget($target_url, $limit, $offset);            
        }
        
        public static function expose() {
            return array('latest');
        }
        
        public static function init() {
            
            Page::create('api', function($page, $subpages){
                
                if ($endpoint = Endpoint::get($subpages[0])) {
                    
                    // What method are we calling
                    list($method_format) = array_slice($subpages, -1);

                    // What API endpoint are we calling?
                    $call = implode('/', array_slice($subpages, 0, -1));
                    Log::debug("Call made to $call endpoint, method $method_format");

                    // Split method_format
                    list($method, $format) = explode('.', $method_format);

                    // Set viewtype 
                    if (!$format) $format='json';
                    Input::set('_vt', $format);
                    Log::debug("Viewtype set to " . Input::get('_vt'));

                    // Sanity check method
                    if (!$method)
                        throw new APIException("No method could be found.");
                    
                    
                    // Call method (assume all methods are part of this class)
                    $mirror = new \ReflectionClass('\pingback2hook\api\API');
                    
                    // Get method, and see what parameters it needs
                    if ((!$mirror_method = $mirror->getMethod($method)) || (!in_array($mirror_method->getName(), API::expose())))
                    {
                        Page::set404();
                        throw new APIException(i18n::w('api:exception:method_not_found', array($definition['class'], $method)));
                    }
                    
                    $method_parameters = array();
                    if ($parameters = $mirror_method->getParameters())
                    {
                        foreach ($parameters as $param) {
                            $value = Input::get($param->name);
                            if ((!$value) && ($param->isDefaultValueAvailable())) // No value, but coded default present
                                $value = $param->getDefaultValue();
                            if (!isset($value))
                                throw new APIException(i18n::w ('api:exception:missing_method_parameter', array($param->name, $method))); // Still no value, throw an exception
                                
                            // We have a value, save it.
                            $method_parameters[] = $value;
                        }
                    }
                    
                    // Execute method call
                    if (count($method_parameters))
                        $result = call_user_func_array ("pingback2hook\api\API::$method", $method_parameters);
                    else 
                        $result = call_user_func ("pingback2hook\api\API::$method");
                    
                    Template::getInstance()->outputPage("{$method}.{$format}", $result);
                    
                }
                else
                    throw new APIException('No endpoint of that definition specified.');
                
            });
        }
    }
}