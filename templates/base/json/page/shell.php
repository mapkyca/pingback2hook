<?php

    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header('Cache-Control: no-cache, must-revalidate');
    header("Pragma: no-cache");
    header('Content-type: application/json; charset=UTF-8');

    $out = new stdClass;
    
    if ($message = \pingback2hook\templates\Template::v('page/elements/messages'))
        $out->messages = json_decode($message);
  //  else {
        if ($output = json_decode($vars['body']))
            $out->body = $output;
        else
            $out->body = $vars['body'];
    //}
    
    echo json_encode($out);