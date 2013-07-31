<?php

/**
 * @file
 * Site template engine.
 * 
 * This provides the default site template system, which loads template
 * files from /templates/VIEW/**
 * 
 * This can be replaced by another template system if needed.
 * 
 * @package templates
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\templates {

    /**
     * Define a template.
     */
    abstract class Template {

        /// Template system in use.
        protected static $template;

        /**
         * Render a view.
         *
         * @param string $view The view to render.
         * @param array $vars Associated array containing variables to be passed to the view.
         * @param string $viewtype Optional view type
         * @return string The rendered view
         */
        abstract public function view($view, array $vars = null, $viewtype = 'json');

        /**
         * Test to see if a view exists.
         *
         * @param string $view The view
         * @param string $viewtype Optional viewtype
         * @return bool
         */
        abstract public function viewExists($view, $viewtype = 'json');

        /**
         * Output a page using the page shell.
         *
         * @param string $title The title of the page.
         * @param string $body Compiled page body
         * @param string $viewtype Optional view type
         * @param bool $return_value If true, output is returned as a return value, otherwise it is echoed (in chunks to avoid Apache buffer overflow issues)
         * @return null|string depending on the value of $return_value
         */
        abstract public function outputPage($title, $body, array $vars = null, $viewtype = 'json', $return_value = false);

        /**
         * Shorthand for rendering a screen.
         * @param type $screen
         * @param type $viewtype
         * @return type 
         */
        public function screen($screen, array $vars = null, $viewtype = 'json') {
            return $this->view('screens/' . trim($screen, '/ '), $vars, $viewtype);
        }

        /**
         * Parse URLs in text.
         */
        public static function parseUrls($text) {
            return preg_replace_callback('/(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i', create_function(
                            '$matches', '
		    $url = $matches[1];
		    $urltext = str_replace("/", "/<wbr />", $url);
		    return "<a href=\"$url\" rel=\"nofollow\" class=\"link\">$urltext</a>";
		'
                    ), $text);
        }

        /**
         * Strip XSS etc from the given output block (which is usually User derived text)
         */
        abstract public function sanitiseOutput($text);

        /**
         * Alias for Template::v()
         */
        public static function v($view, array $vars = null, $viewtype = 'json') {
            return static::getInstance()->view($view, $vars, $viewtype);
        }

        /**
         * Return the current template engine.
         * 
         * @throws SubsystemFactoryException if no template is defined.
         * @return Template
         */
        public static function &getInstance() {
            if (!self::$template)
                self::setInstance(\pingback2hook\core\SubsystemFactory::factory('template'));

            return self::$template;
        }

        /**
         * Set the default templating engine.
         * @param Template $template 
         */
        public static function setInstance(Template $template) {
            self::$template = $template;
        }

    }

}