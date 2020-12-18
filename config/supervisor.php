<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supervisor Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Supervisor will be accessible from. If this
    | setting is null, Supervisor will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Supervisor Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Supervisor will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => 'supervisor',

    /*
    |--------------------------------------------------------------------------
    | Supervisor Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Supervisor route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Supervisor worker
    | may consume before it is terminated and restarted. You should set
    | this value according to the resources available to your server.
    |
    */

    'memory_limit' => 32,

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('log'),
        ],
    ],

    'view' => ['reverse'],

    'resolvers' => [
        'reverse' => [
            'disk' => 'local',
            'resolver' => 'reverse',
            'extension' => 'log'
        ],
        'positive' => [
            'disk' => 'local',
            'resolver' => 'positive',
            'extension' => 'log'
        ]
    ],

    'handler' => [

    ]
];
