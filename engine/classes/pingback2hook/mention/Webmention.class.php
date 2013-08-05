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

    use pingback2hook\core\Page as Page;
    use pingback2hook\core\Input as Input;
    use pingback2hook\core\Log as Log;
    use pingback2hook\endpoints\Endpoint as Endpoint;
    use pingback2hook\templates\Template as Template;

    class Webmention extends Mention {
        

        public static function endpoint($page, $subpages) {

            if ($endpoint = Endpoint::get($subpages[0])) {

                $source_url = Input::get('source');
                $target_url = Input::get('target');

                // Do we have a source and target URL?
                if ($source_url && $target_url) {

                    // Check we haven't already got this one registered.
                    if (self::isTargetRegistered($source_url, $target_url))
                        throw new AlreadyRegisteredException("Target $target_url has already been registered.");

                    // Check whether target is in source url
                    if (!$details = self::checkSourceUrl($source_url, $target_url))
                        throw new NoLinkFoundException("$target_url not found in $source_url");

                    // Append configuration details
                    $details['endpoint'] = $subpages[0];

                    // Save
                    if (self::saveMention($target_url, $source_url, $details)) {

                        Log::debug("Webmention OK");

                        header('HTTP/1.1 202 Accepted');

                        Template::getInstance()->outputPage("Webmention of $target_url from $source_url", array(
                            'result' => 'Ok.'
                        ));
                    }
                    else
                        throw new TargetNotSupportedException("Problem saving mention to $target_url from $source_url.");
                }
                else
                    throw new SourceNotFoundException("Source and target variables missing ($target_url from $source_url).");
            }
            else
                throw new TargetNotFoundException('No endpoint of that definition specified.');
        }

        public function init() {

            Mention::init();
            
            Page::create('webmention', '\pingback2hook\mention\Webmention::endpoint');
        }

    }

}