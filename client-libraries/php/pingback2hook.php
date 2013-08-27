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
        
        $query = $this->host . 'api/' . $this->endpoint . '/'.  $method . '.json?' . implode('&', $qp);
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL, $query);
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
        if ($results = $this->getLatest($target_url, $limit, $offset)) {
            
            $out = array();
            
            foreach ($results->rows as $result) {
             
                ob_start();
                
                $entity = $result->value;
                $details = $entity->details;
                
                $mf2 = null;
                if (isset($details->mf2)) 
                    $mf2 = $details->mf2;
                
                ?>

<div id="<?= $result->id; ?>" class="p2h-item h-cite p-comment  <?= $details->handler; ?>">
    <?php 
        if ($mf2) {
            // MF2 details found
            
            foreach ($mf2->items as $item) {
                                
                // Find the entry
                if (in_array('h-entry', $item->type)) {
                    if (!$author)
                        $author = $item->properties->author[0]->properties->name[0];
                    
                    if (!$home)
                        $home = $item->properties->author[0]->url[0];
                    
                    if (!$photo)
                        $photo = $item->properties->author[0]->properties->photo[0];
                    
                    if (!$content)
                        $content = strip_tags($item->properties->content[0], '<p><br><a>');
                }
            }
            
            //if (!$home)
            //    $home = $mf2->rels->home[0];
            
            ?>
    
    <div class="p2h-author-icon ">
        <address class="p-author author vcard h-card">
            <img src="<?= $photo; ?>" class="u-photo" height="50" width="50" />
            <cite class="fn p-name"><a href="<?= $home; ?>" rel="external nofollow" class="u-url url"><?= $author; ?></a></cite> 
            <span class="says">says:</span>        
        </address>
    </div>
    <div class="p2h-comment-details">
        <p class="comment-meta">
            <a href="<?= $entity->source_url; ?>" rel="nofollow bookmark" class="note-published u-url">
                <time class="dt-published published dt-updated updated" datetime="<?= date('c', $entity->unix_timestamp); ?>"><?= date('G:i j', $entity->unix_timestamp); ?><sup><?= date('S', $entity->unix_timestamp); ?></sup> <?= date('F Y', $entity->unix_timestamp); ?></time>
            </a>
        </p>
        <p class="p-summary"><?= $content; ?></p>
    </div>
                
            <?php
        } else {
            // Just a straight ping
            ?>
            <p>
                <a href="<?= $entity->source_url; ?>" rel="nofollow bookmark" class="note-published u-url"><?= $details->title; ?></a> mentioned <a class="u-url u-in-reply-to" href="<?= $entity->target_url; ?>">this</a> on
                <time class="dt-published published dt-updated updated" datetime="<?= date('c', $entity->unix_timestamp); ?>"><?= date('G:i j', $entity->unix_timestamp); ?><sup><?= date('S', $entity->unix_timestamp); ?></sup> <?= date('F Y', $entity->unix_timestamp); ?></time>
            </p>
    
            <?php
        }
    // class for each type, if pingback/mention, if mf2 then parse rich content
    
    ?>
</div>

                <?php
                
                $out[] = ob_get_clean();
                
            }
            
            return $out;
        }
        
        return false;
    }
}