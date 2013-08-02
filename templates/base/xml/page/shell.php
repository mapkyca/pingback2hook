<?php

    $data = "<?xml version=\"1.0\"?>\n{$vars['body']}";

    header("Content-Type: text/xml");
    header("Content-Length: " . strlen($data));

    echo $data;
