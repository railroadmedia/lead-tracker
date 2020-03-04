<?php

return [
    // brand
    'brand' => 'brand', // this is what will go in the brand column for requests to this project

    // database
    'database_connection_name' => 'mysql',
    'data_mode' => 'host', // 'host' or 'client', hosts do the db migrations, clients do not
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',

    // these are the urls/methods which should be captured by the middleware and processed by this package

    // the data map tells the middleware which input variables map to which attributes that are required
    // by the package
    'requests_to_capture' => [
        [
            // these 3 are used to match the request
            'path' => '/my-form-submission-path',
            'method' => 'post',
            'form_name' => 'my-lead-form-1',

            // since different forms may have different input names it must be configurable
            // these keys must all be set for every request you wish to capture
            'input_data_map' => [
                'email' => 'my_email_input_name',
                'maropost_tag_name' => 'my_maropost_tag_name_input_name',
                'form_name' => 'my_form_name_input_name',
                'utm_source' => 'my_utm_source_input_name',
                'utm_medium' => 'my_utm_medium_input_name',
                'utm_campaign' => 'my_utm_campaign_input_name',
                'utm_term' => 'my_utm_term_input_name',
            ],
        ],
        [
            'path' => '/my-other-form-submission-path',
            'method' => 'put',
            'form_name' => 'my-lead-form-2',

            'input_data_map' => [
                'email' => 'my_other_email_input_name',
                'maropost_tag_name' => 'my_other_maropost_tag_name_input_name',
                'form_name' => 'my_other_form_name_input_name',
                'utm_source' => 'my_other_utm_source_input_name',
                'utm_medium' => 'my_other_utm_medium_input_name',
                'utm_campaign' => 'my_other_utm_campaign_input_name',
                'utm_term' => 'my_other_utm_term_input_name',
            ],
        ],
    ]
];
