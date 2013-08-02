<?php

/**
 * @file
 * 
 * XML Elements
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\xml {

    /**
     * @class XmlElement
     * A class representing an XML element for import.
     */
    class XmlElement {

        /** The name of the element */
        public $name;

        /** The attributes */
        public $attributes;

        /** CData */
        public $content;

        /** Child elements */
        public $children;

    }


}