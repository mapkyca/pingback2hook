<?php

/**
 * @file
 * 
 * PHP Pingback2Hook library.
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */


class Pingback2Hook {
    
    private $host;
    private $secret;
    private $endpoint;
    
    /**
     * Create the API interface
     * @param type $host Host to talk to
     * @param type $secret Secret key to pass
     */
    public function __construct($host, $endpoint, $secret) {
        $this->host = trim($host, ' /') . '/';
        $this->secret = $secret;
        $this->endpoint = trim($endpoint, ' /');
    }
    
    /**
     * Execute a raw query.
     * @param type $method
     * @param array $parameters
     */
    protected function query($method, array $parameters = null) {
        
        // Construct query parameters
        $qp = array();
        if ($parameters) {
            
            foreach ($parameters as $key => $val)
            {
                $qp[] = urlencode($key) . '=' . urlencode($val);
            }
        }
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL, $this->host . 'api/' . $this->endpoint . $method . '.json?' . implode('&', $qp));
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, "pingback2hook PHP client library");
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
            'X-PINGBACK2HOOK-SECRET: ' . $this->secret
        ));

        $buffer = curl_exec($curl_handle);
        $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        curl_close($curl_handle);
        
        return json_decode($buffer);
    }
    
    /**
     * Retrieve the latest entries.
     * @param type $limit
     * @param type $offset
     */
    public function getLatest($target_url, $limit = 10, $offset = 0) {
        
        if ($results = $this->query('latest', array(
            'target_url' => $target_url,
            'limit' => $limit,
            'offset' => $offset
        )))
        {
            return $results;
        }
        
        return false;
    }
    
    /**
     * Retrieve the latest entries and pass the result to nice stylable HTML
     * @param type $limit
     * @param type $offset
     */
    public function getLatestAsHTML($target_url, $limit = 10, $offset = 0) {
        if ($results = $this->getLatest($limit, $offset)) {
            
            $out = array();
            
            foreach ($results['rows'] as $result) {
             
                ob_start();
                
                $details = $result->details;
                
                $mf2 = null;
                if (isset($details->mf2)) 
                    $mf2 = $details->mf2;
                ?>

<div id="<?= $result->id; ?> <?= $details['handler']; ?>" class="p2h-item h-cite p-comment">
    <?php 
        if ($mf2) {
            // MF2 details found
            
            
            
            ?>
                
            <?php
        } else {
            // Just a straight ping
            ?>
            <p>
                <a href="<?= $result->source_url; ?>" rel="nofollow bookmark" class="note-published u-url"><?= htmlentities($details->title); ?></a> mentioned <a href="u-url u-in-reply-to" href="<?= $result->target_url; ?>">this</a> on
                <time class="dt-published published dt-updated updated" datetime="<?= date('c', $result->unix_timestamp); ?>"><?= date('G:H j', $result->unix_timestamp); ?><sup><?= date('S', $result->unix_timestamp); ?></sup> <?= date('F Y', $result->unix_timestamp); ?></time>
            </p>
    
            <?php
        }
    // class for each type, if pingback/mention, if mf2 then parse rich content
    
    ?>
</div>

                <?php
                
                $out[] = ob_get_clean();
                
            }
        }
        
        return false;
    }
}