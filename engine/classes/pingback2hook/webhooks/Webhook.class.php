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
    use pingback2hook\storage\nosql\NoSQLStorage as NoSQLStorage;
    use pingback2hook\storage\nosql\CouchDB as CouchDB;
    use pingback2hook\core\Log as Log;
    use pingback2hook\endpoints\Endpoint as Endpoint;
    
    class Webhook {
        
        
        public static function init() {
            
            // Listen for save events in order to trigger webhooks
            Events::register('mention', 'save', function($namespace, $event, $parameters) {
               
                $couch = CouchDB::getInstance();
                
                Log::debug("Sending webhook pings...");
                
                if ($endpoint = Endpoint::get($parameters['endpoint']))
                {

                    $json = json_encode($parameters);

                    foreach ($endpoint['webhooks'] as $url) {
                        
                        Log::debug("Sending webhook to $url");

                        $headers = array(); 
                        $headers['Content-Length'] = strlen($json);
                        $headers['Content-Type'] = 'application/json';
                        
                        $headers_str = "";

                        foreach ($headers as $k => $v) {
                                $headers_str .= trim($k) . ": " . trim($v) . "\r\n";
                        }

                        $http_opts = array(
                                'method' => 'POST',
                                'header' => trim($headers_str),
                                'content' => $json
                        );

                        $opts = array('http' => $http_opts);
                        $context = stream_context_create($opts);

                        $result = file_get_contents($url, false, $context);
                        
                        // Save result of pingback
                        $ping = new \stdClass();
                        $ping->on_uuid = $parameters['uuid'];
                        $ping->on_rev = $parameters['couch_rev'];
                        $ping->response_headers = $http_response_header;
                        
                        $couch->store('wh-' . sha1($parameters['uuid'] . $url), $ping);
                        
                    }
                    
                }
                else 
                    Log::error ("No endpoints could be found to send pingback, this shouldn't happen.");
                
                
            });
        }
    }
}