<?php


    require_once('pingback2hook.php');

    define('HOST', 'https://localhost/'); // Host to talk to
    define('ENDPOINT', 'example'); // Endpoint to talk to
    define('SECRET', 'example'); // Secret key
    
    define('PERMALINK', 'http://mytarget.com/permalink/'); // Url in question
    define('MAX_ITEMS', 10); // Number of items to show
    define('OFFSET', 0); // For pagination
?>
<html>
    <head>
        <title>Example pingbacks</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/pingback2hook.css">
        <style type="text/css">
            
        </style>
    </head>
    <body>
        
        <h1>Examples of how pingbacks might look</h1>
        <div class="pingbacks">
            <?php
            
                $p2h = new Pingback2Hook(HOST, ENDPOINT, SECRET);
                
                if ($pingbacks = $p2h->getLatestAsHTML(PERMALINK, MAX_ITEMS, OFFSET)) {
                    
                    foreach ($pingbacks as $ping) {
                        echo $ping;
                    }
                    
                }
                else
                {
                    echo "No pingbacks on " . HOST . " yet!";
                }
            ?>
        </div>        
    </body>
</html>
