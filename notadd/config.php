<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-22 23:50
 */
return [
    'database' => [
        'fetch' => PDO::FETCH_CLASS,
        'default' => 'mysql',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => storage_path('database.sqlite'),
                'prefix' => '',
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'forge',
                'username' => 'forge',
                'password' => '',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => false,
            ],
            'pgsql' => [
                'driver' => 'pgsql',
                'host' => 'localhost',
                'database' => 'forge',
                'username' => 'forge',
                'password' => '',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
            ],
            'sqlsrv' => [
                'driver' => 'sqlsrv',
                'host' => 'localhost',
                'database' => 'forge',
                'username' => 'forge',
                'password' => '',
                'charset' => 'utf8',
                'prefix' => '',
            ],
        ],
        'migrations' => 'migrations',
        'redis' => [
            'cluster' => false,
            'default' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
            ],
        ],
    ]
];