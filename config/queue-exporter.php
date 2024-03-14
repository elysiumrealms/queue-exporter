<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exported File Life Span
    |--------------------------------------------------------------------------
    |
    | This configuration value allows you to customize the life span in seconds
    | of the exported files. If the exported files are not downloaded within
    | the given time, they will be deleted from the storage.
    |
    */

    'life_span' => env('QUEUE_EXPORTER_LIFE_SPAN', 86400 * 30),


    /*
    |--------------------------------------------------------------------------
    | Queue name
    |--------------------------------------------------------------------------
    | This configuration value allows you to customize the queue which should
    | be sent to. You can set it to any queue name you want.
    |
    */

    'queue' => env('QUEUE_EXPORTER_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | QueueExporter Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration value allows you to customize the storage options
    | for QueueExporter, such as the database connection that should be used
    | by QueueExporter's internal database models which store tokens, etc.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
    ],

];
