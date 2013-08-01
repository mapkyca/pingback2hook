<?php

namespace pingback2hook\i18n {

    $errors = array(
        // System
        'exception:title' => 'Exception',
        // Pages
        'page:exception:notfound' => 'Page \'%s\' not found.',
        // API
        'api:exception:class_not_specified' => 'Endpoint definition "%s" has no class entry defined!',
        'api:exception:class_not_found' => 'Class "%s" could not be found.',
        'api:exception:api_not_found' => 'The requested API (\'%s\') was not found on this server, please check your definition file.',
        'api:exception:no_method' => 'No method call given, use the format like http://server.com/api/path/to/endpoint/methodcall.json',
        'api:exception:method_not_found' => 'Method %s::%s could not be found',
        'api:exception:missing_method_parameter' => 'Missing parameter "%s" missing in method "%s"',
        // Plugin
        'plugin:exception:no_constructor' => 'I don\'t know how to create "%s" as the class has no constructor',
        'plugin:exception:missing_construction_parameter' => 'Missing parameter "%s" missing in class %s\'s constructor',
        'plugin:exception:could_not_create_instance' => 'Could not create new instance of class "%s"',
        // Couch DB
        'couchdb:exception:curl_not_installed' => 'Sorry, cURL is not installed.',
        'couchdb:exception:no_result' => 'No result returned from server',
        'couchdb:exception:result_not_json' => 'Result returned was not valid JSON',
        // Mention Exceptions
        'exception:pingback2hook:mention:AlreadyRegisteredException' => 'already_registered',
        'exception:pingback2hook:mention:TargetNotSupportedException' => 'target_not_supported',
        'exception:pingback2hook:mention:TargetNotFoundException' => 'target_not_found',
        'exception:pingback2hook:mention:SourceNotFoundException' => 'source_not_found',
        'exception:pingback2hook:mention:NoLinkFoundException' => 'no_link_found',
    );


    Basic::register($errors, 'en');
}