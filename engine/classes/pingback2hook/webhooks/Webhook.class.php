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
                
                if ($endpoint = Endpoint::get($parameters['endpoint']))
                {
                    foreach ($endpoint['webhooks'] as $url) {
                        
                        $json = json_encode($parameters);

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
                                'content' => $query
                        );

                        $opts = array('http' => $http_opts);
                        $context = stream_context_create($opts);

                        $result = file_get_contents($url, false, $context);
                        
                    }
                    
                }
                
                
            });
        }
    }
}