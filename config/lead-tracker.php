<?php

return [
    // brand
    'brand' => 'brand', // this is what will go in the brand column for requests to this project

    // database
    'database_connection_name' => 'mysql',
    'database_name' => 'mydb',
    'database_user' => 'root',
    'database_password' => 'root',
    'database_host' => 'mysql',
    'database_driver' => 'pdo_mysql',
    'database_in_memory' => false,
    'data_mode' => 'host', // 'host' or 'client', hosts do the db migrations, clients do not

    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',

    // these are the urls/methods which should be captured by the middleware and processed by this package
    'requests_to_capture' => [
        [
            'path' => '/my-form-submission-path',
            'method' => 'post',
        ],
        [
            'path' => '/my-other-form-submission-path',
            'method' => 'put',
        ],
    ]
];
