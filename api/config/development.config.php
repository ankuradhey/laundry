<?php

return array(
    // Development time modules
    'modules' => array(
        'ZF\Apigility\Admin',
        'ZF\Configuration',
    ),
    // development time configuration globbing
    'module_listener_options' => array(
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}-development.php')
    )
);