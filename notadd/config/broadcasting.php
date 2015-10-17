<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 20:15
 */
return [
    'default' => 'pusher',
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => '',
            'secret' => '',
            'app_id' => '',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
        'log' => [
            'driver' => 'log',
        ],
    ],
];