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
    use pingback2hook\storage\nosql\NoSQLStorage as NoSQLStorage;
    use pingback2hook\storage\nosql\CouchDB as CouchDB;
    
    abstract class Mention {
        
        /** 
         * Create db views if necessary
         */
        protected static function createViews() {
            
            $uuid = '_design/mention';
            $couch = CouchDB::getInstance();
            
            // See if we have a map function - map
            if (!$map = $couch->retrieve($uuid)) {
            
                // Now we need to create view
                $view = new \stdClass();
                $view->views = new \stdClass();
                $view->views->mentions_on_target = new \stdClass();
                
                $view->views->mentions_on_target->map = "function (doc) {
                        if (doc.target_url && doc.source_url) {
                            emit(doc.target_url, doc);
                        }
                    }
                ";
                
                $couch->store($uuid, $view);
                
            }
            
        }
        
        public static function init() {
            self::createViews();
        }
        
        /**
         * Retrieve a specific ping based on it's uuid (generated from target and source).
         * @param type $uuid
         * @return type
         */
        public static function get($uuid) {
            return $couch->retrieve($uuid);
        }
        
        /**
         * Retrieve all mentions on a given target URL.
         * @param type $target_url
         * @param type $limit
         * @param type $offset
         * @return type
         */
        public static function mentionsOnTarget($target_url, $limit = 10, $offset = 0, $descending = true) {
            
           $couch = CouchDB::getInstance();
           return $couch->retrieve('_design/mention/_view/mentions_on_target', array('key' => "\"$target_url\"", 'limit' => $limit, 'skip' => $offset, 'descending' => $descending ? 'true' : 'false'));
            
        }
        
        /**
         * Generate a UUID out of source and target
         * @param type $source_url
         * @param type $target_url
         * @return type
         */
        protected static function uuid($source_url, $target_url) { 
            return 'mention-' . sha1($target_url . $source_url); 
        }
        
        /** 
         * Check whether a target URL is registered.
         * @param type $target
         */
        public static function isTargetRegistered($source_url, $target_url) {
            $uuid = self::uuid($source_url, $target_url);
            $couch = CouchDB::getInstance();
            
            // See if this mention already exists
            if ($latest = $couch->retrieve($uuid))
                return true;
            
            return false;
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

                    return self::parseSource($source_url, $target_url); // Target found in source, return some data about the page
                    
                }
                return false;
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
               
            // Save in couch
            $uuid = self::uuid($source_url, $target_url);
            $couch = CouchDB::getInstance();
            
            if (!$details) $details = array();
            $saved = new \stdClass();
            $saved->target_url = $target_url;
            $saved->source_url = $source_url;
            $saved->unix_timestamp = time();
            $saved->details = $details;
            
            $rev = $couch->store($uuid, $saved);
            if (!$rev)
                return false;
            
            $details['couch_rev'] = $rev;
            $details['uuid'] = $uuid;
            $details['unix_timestamp'] = $saved->unix_timestamp;
            
            // Trigger an event
            Events::trigger('mention', 'save', array_merge(array('target_url' => $target_url, 'source_url' => $source_url), $details));
            
            return true;
        }
        
    }
}