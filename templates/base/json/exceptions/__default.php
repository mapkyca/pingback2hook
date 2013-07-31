<?php

\pingback2hook\core\Page::set400();

echo json_encode(array(
    'error' => \pingback2hook\i18n\Basic::w('exception:' . str_replace('\\', ':', get_class($vars['exception']))),
    'error_description' => $vars['exception']->getMessage()
));
