<?php

    $export = new stdClass();
    $object = $vars['object'];
    
    $reflect = new ReflectionClass($object);
    $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
    
    foreach ($props as $prop) {
        $name = $prop->getName();
        $export->$name = $object->$name;
    }
    
    // Add some extra, but handy information
    $export->url = $object->getUrl();
    
    echo json_encode($export);