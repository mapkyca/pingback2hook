<?php 
    echo json_encode(array(
        'status' => 'exception',
        'class' => get_class($vars['exception']),
        'details' => $vars['exception']->getMessage()
            ));
