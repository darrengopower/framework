<?php
return [
    'driver' => 'eloquent',
    'model' => Notadd\Foundation\Auth\Models\User::class,
    'table' => 'users',
    'password' => [
        'email' => 'admin::emails.password',
        'table' => 'password_resets',
        'expire' => 60,
    ],
];