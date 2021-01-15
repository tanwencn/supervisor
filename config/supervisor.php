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
    | This is the URI prefix for Supervisor.
    |
    | 这个是Supervisor的URI前缀
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
    | Timeout (seconds)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum seconds of single request the Supervisor
    |
    | 解析文件的单次请求最大解析时间，也意味着你在视图中将要等待的最大秒数。
    | 超时后会进行断点轮询，建议设置为0-3秒。
    |
    */

    'single_time' => 1,

    /*
    |--------------------------------------------------------------------------
    | disks
    |--------------------------------------------------------------------------
    |
    | This is the Filesystem drive configuration.
    | for details, please refer to config/ Filesystem.php and pay attention to the key values.
    |
    | 这个是Filesystem驱动配置，具体配置项参考config/filesystem.php，注意key值不要重复。
    |
    */

    'disks' => [
        'supervisor_local' => [
            'driver' => 'local',
            'root' => storage_path('logs'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | resolvers
    |--------------------------------------------------------------------------
    |
    | is a file parsing driver.
    | that provides Laravel log reverse and positive parsers by default The
    | Custom drivers can be configured in the handler item of config/supervisor.php
    |
    | 这个是文件解析驱动，默认提供Laravel日志的逆向(reverse)和正向(positive)解析器
    | 自定义驱动可以在config/supervisor.php 的handler项进行配置
    |
    */

    'resolvers' => [
        'default' => [
            'mode' => 'filesystem',
            'disk' => 'supervisor_local',
            'order' => 'asc',
            'regular' => [
                'expres' => '/\[(\d{4}[-\d{2}]{2}.*?)\] (.+?)\.(.+?):(.*)/',
                'output' => ['date', 'env', 'level', 'code', 'more']
            ],
            'header' => ['id', 'date', 'env', 'level', 'code', 'fullText'],
            'extension' => 'log'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | view
    |--------------------------------------------------------------------------
    |
    | This is the list of parsers to display in the Supervisor view list
    |
    | 这个是Supervisor视图列表中要显示出来的解析器列表
    |
    */
    'view' => ['default'],

    /*
    |--------------------------------------------------------------------------
    | deep_base_router
    |--------------------------------------------------------------------------
    |
    | If your APP is not in the root directory of domain name access road jin.
    | Just configure this prefix
    |
    | 如果你的应用不在域名访问路劲的根目录下，就配置上这个前缀吧
    |
    */
    'deep_base_router' => ''
];
