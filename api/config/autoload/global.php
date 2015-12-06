<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 * resources.db.params.host = 166.62.8.8
resources.db.params.username = laundryUser
resources.db.params.password = Laundry@123
resources.db.params.dbname = laundryUser
 */
return array(
     'db' => array(
         'driver'         => 'Pdo',
         'dsn'            => 'mysql:dbname=laundry_new;host=localhost',
         'username'       => 'root',
         'password'         => '',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),
     ),
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
         ), 'aliases' => array(
            'db' => 'Zend\Db\Adapter\Adapter',
        ),
     ),
);
