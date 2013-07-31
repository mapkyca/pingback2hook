<?php

/**
 * @file
 * Site wide exceptions.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core\exceptions {

    /**
     * Base class for all system exceptions.
     * 
     * This class provides a nice way to trap and extend error messages triggered by a thrown exception.
     */
    class SystemException extends \Exception {

        /**
         * Render the exception using the views system.
         */
        public function __toString() {
            $class = strtolower(get_class($this));
            
            \pingback2hook\core\Log::debug("Exception thrown: " . $this->getMessage());

            $content = \pingback2hook\templates\Template::v("exceptions/$class", array('exception' => $this));
            if ($content)
                return $content;

            $content = \pingback2hook\templates\Template::v('exceptions/__default', array('exception' => $this));
            if ($content)
                return $content;

            return false;
        }

    }

}