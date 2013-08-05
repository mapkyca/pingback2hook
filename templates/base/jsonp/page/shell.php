<?php

    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header('Cache-Control: no-cache, must-revalidate');
    header("Pragma: no-cache");
    header('Content-type: application/x-javascript; charset=UTF-8');

    if ($output = json_decode($vars['body']))
        $out = $vars['body'];
    else
        $out = json_encode($vars['body']);
    
    echo pingback2hook\core\Input::get('callback') . "($out)";
