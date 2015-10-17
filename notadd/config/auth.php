<?php
return [
    'driver' => 'eloquent',
    //'model' => App\User::class,
    'table' => 'users',
    'password' => [
        'email' => 'emails.password',
        'table' => 'password_resets',
        'expire' => 60,
    ],
];