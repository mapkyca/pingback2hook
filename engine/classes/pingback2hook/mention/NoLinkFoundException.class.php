<?php

/**
 * @file
 * Mention exception.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\mention {

    class NoLinkFoundException extends MentionException {
        public function __construct($message, $code, $previous) {
            $code = 17;
            parent::__construct($message, $code, $previous);
        }
    }

}