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
    
    use pingback2hook\core\Events as Events;
    
    
    abstract class Mention {
        
        /** 
         * Check whether a target URL is registered.
         * @param type $target
         */
        public static function isTargetRegistered($target) {
            
        }
        
        /**
         * Parse source page
         * @param type $data The page source
         */
        protected static function parseSource($data, $target_url) {
            
            $details = array();
            
            // Get title
            if (preg_match("/<title>(.*)<\/title>/imsU", $data, $m)) 
                $details['title'] = $m[1];
                        
            // Get extract (TODO: Do this nicer)
            $strpos = strpos($data, $target_url);
            if ($strpos!==false)
            {
                $a = 0;
                if ($strpos>300) $a=$strpos-300;

                $extract = strip_tags(substr($data, $a, 600));

                if ($extract) {
                        $hwp = strlen($extract) / 2;
                        $extract = substr($extract, $hwp - 75, 150);

                        $extract = "..." . trim($extract) . "...";
                        
                        $details['extract'] = $extract;
                }
            }
            
            // Return result of hook, lets see if there are any other things want to add data
            return Events::trigger('mention', 'parsesource', array('return' => $details));
            
        }
        
        /**
         * Check for the existence of $source in $target.
         * @param type $target
         * @param type $source
         * @return array of data parsed from the source
         */
        protected static function checkSourceUrl($source_url, $target_url) {
            if ($source = file_get_contents($source_url)) {
                
                preg_match_all('/(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i', $source, $matches);
                if ((in_array($target_url, $matches[0])) && (strpos($http_response_header[0], '4') === false)) {

                    return self::parseSource($source, $target_url); // Target found in source, return some data about the page
                    
                }
            }
            
            throw new SourceNotFoundException("Could not load $source_url");
        }
        
        /**
         * Save a mention to target from source, with optional details
         * @param type $target_url
         * @param type $source_url
         * @param array $details
         */
        protected static function saveMention($target_url, $source_url, array $details = null) {
           
            
            // TODO : Save
            
            
            // Trigger an event
            if (!$details) $details = array();
            Events::trigger('mention', 'saved', array_merge(array('target_url' => $target_url, 'source_url' => $source_url), $details));
            
            return true;
        }
        
        
        // validate url function
        
        // save url function
        
    }
}