<?php

/**
 * @file
 * Virtual page handling methods.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    /**
     * Virtual page methods.
     */
    class Page {

        private static $pages;

        /**
         * Create a new virtual page or file and assign a handler to it.
         * 
         * A page handler should be a function defined as :
         * 
         * 	handler($page, array $subpages);
         * 
         * @param type $page Page identifier, e.g. /foo/bar/
         * @param type $handler Handler function or static method
         */
        public static function create($page, $handler) {
            if (!isset(self::$pages))
                self::$pages = array();

            self::$pages[$page] = $handler;

            return true;
        }

        /**
         * Handle a virtual page.
         *
         * @param string $page The page
         * @return bool
         */
        public static function call($page) {
            // Work out which page
            $pages = explode('/', $page);

            $key = "";
            foreach ($pages as $p) {
                $key .= $p;
                if ((isset(self::$pages[$key])) || (isset(self::$pages["$key/"])))
                    break;

                $key .= "/";
            }

            // Tokenise input variables
            $query = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1);
            if (isset($query)) {
                parse_str($query, $query_arr);
                if (is_array($query_arr)) {
                    foreach ($query_arr as $name => $val) {
                        Input::set($name, $val);
                    }
                }
            }

            // We have a page registered for this
            if ($key) {
                // Get sub pages below the handler, pass these as subpage variables.
                $pages = substr($page, strlen($key));
                $pages = trim($pages, "/?");
                $pages = explode('/', $pages);

                // Execute handler
                $handler = self::$pages[$key];
                if (!$handler)
                    $handler = self::$pages["$key/"]; // See if this was registered with a trailing slash

                if (is_callable($handler)) {
                    // Set the context of a page
                    self::setContext($key);

                    if (call_user_func($handler, $key, $pages) !== false)
                        return true;
                }

                return false;
            }
        }

        /**
         * Sometimes pages have a context.
         * 
         * Contexts define an arbitrary grouping for pages. This is useful to set menu options etc.
         * @param string $context The context
         */
        public static function setContext($context) {
            \pingback2hook\System::$runtime->page_context = $context;
        }

        /**
         * Retrieve the current page context.
         * @return string
         */
        public static function getContext() {
            return \pingback2hook\System::$runtime->page_context;
        }

        /**
         * Forward the browser to a specific location.
         * 
         * Forwards the browser session using a forward header. Note that successful execution of this
         * function will stop execution on the page.
         *
         * @param url $location The location to forward to, if this isn't the full url then it is assumed
         * 						relative to $CONFIG->wwwroot. A blank location will forward to $CONFIG->wwwroot.
         * @param int $code Optional HTTP code to use defining the forward, defaults to 302
         * @return false if headers have already been sent.
         */
        public static function forward($location = "", $code = 302) {
            if (!headers_sent()) {

                if ($location === REFERER) {
                    $location = $_SERVER['HTTP_REFERER'];
                }

                if ((substr_count($location, 'http://') == 0) && (substr_count($location, 'https://') == 0))
                    $location = self::$config->wwwroot . $location;

                header("Location: {$location}", true, $code);
                exit;
            }

            return false;
        }

        /**
         * Construct a URL from array components (basically an implementation of http_build_url() without PECL.
         * 
         * @todo Move somewhere sensible
         * @param array $url
         * @return string 
         */
        public static function buildUrl(array $url) {
            $page = $url['scheme'] . "://";

            // user/pass
            if ((isset($url['user'])) && ($url['user']))
                $page .= $url['user'];
            if ((isset($url['pass'])) && ($url['pass']))
                $page .= ":" . $url['pass'];
            if (($url['user']) || $url['pass'])
                $page .="@";

            $page .= $url['host'];

            if ((isset($url['port'])) && ($url['port']))
                $page .= ":" . $url['port'];

            $page .= $url['path'];

            if ((isset($url['query'])) && ($url['query']))
                $page .= "?" . $url['query'];


            if ((isset($url['fragment'])) && ($url['fragment']))
                $page .= "#" . $url['fragment'];


            return $page;
        }

        /**
         * Return the full URL of the current page.
         *
         * @param $tokenise bool If true then an exploded tokenised version is returned.
         * @return url|array
         */
        public static function currentUrl($tokenise = false) {
            $url = parse_url(self::$config->wwwroot);
            $url['path'] = $_SERVER['REQUEST_URI'];

            // Is HTTPS?
            if (Secure::isSSL())
                $url['scheme'] = 'https';

            if ($tokenise)
                return $url;

            return self::buildUrl($url);
        }

        /**
         * Set 400 headers
         */
        public static function set400() {
            header("HTTP/1.1 400 Bad Request");
            header("Status: 400");
        }

        /**
         * Set 404 headers
         */
        public static function set404() {
            header("HTTP/1.1 404 Not Found");
            header("Status: 404");
        }

        /**
         * Forbidden
         */
        public static function set403() {
            header("HTTP/1.1 403 Forbidden");
            header("Status: 403");
        }

    }

    /* Define some shortcut definition */

/// Forward browser back to original location
    define('REFERRER', -1);
/// Forward browser back to original location
    define('REFERER', -1);
}
