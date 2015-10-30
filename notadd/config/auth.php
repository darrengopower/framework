<?php
return [
    'driver' => 'eloquent',
    'model' => Notadd\Foundation\Auth\Models\User::class,
    'table' => 'users',
    'password' => [
        'email' => 'emails.password',
        'table' => 'password_resets',
        'expire' => 60,
    ],
];