<?php

/**
 * @file
 * 
 * Handler for virtual pages.
 * 
 * Virtual pages are pages which don't physically exist, rather they are 
 * provided virtually and dynamically by classes and library functions in
 * the engine.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    require_once(dirname(dirname(__FILE__)) . '/start.php');

    $page = Input::get('page');

    header("X-Handler: Pingback2Hook page handler");

    if (!Page::call($page)) {
        Page::set404();

        throw new \home_api\core\exceptions\PageNotFoundException(sprintf(\home_api\i18n\i18n::w('page:exception:notfound'), $page));
    }
}