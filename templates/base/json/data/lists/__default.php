<?php

    $objects = $vars['objects'];
    $export = array();
    
    foreach ($objects as $object) {
        $export[] = json_decode($object->view($vars));
    }
    
    echo json_encode($export);
    
    