<?php
    require_once(dirname(__FILE__) . '/engine/start.php');

    // Example webmention endpoint
    $webmention = pingback2hook\core\Environment::getWebRoot() . 'webmention/example/';
    header('Link: <'.$webmention.'>; rel="http://webmention.org/"');
    
    // Pingback
    $pingback = pingback2hook\core\Environment::getWebRoot() . 'pingback/example/';
    header('X-Pingback: ' . $pingback);
    
?><html>
    <head>
        <title>Generic Pingback/Webmention endpoint : Pingback2Hook</title>
        <link href="<?= $webmention; ?>" rel="http://webmention.org/" />
        <link rel="pingback" href="<?= $pingback; ?>" />
    </head>
    <body>
        <h1>Generic Pingback/Webmention endpoint</h1>
        <p>This is a generic pingback/webmention endpoint created by <a href="http://www.marcus-povey.co.uk">Marcus Povey</a>. It lets you easily add pingback support to static pages, query it, and notify other services via webhooks.</p>
        <p>Go see the <a href="https://github.com/mapkyca/pingback2hook">project on github</a>.</p>
        
        <p><small><em>Note, this page has endpoints for 'example' (unless you've disabled them), which can use to test things if you like.</em></small></p>
    </body>
</html>