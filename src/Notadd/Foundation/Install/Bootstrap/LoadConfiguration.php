<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Install\Bootstrap;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Notadd\Foundation\Auth\Models\User;
class LoadConfiguration {
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $app->instance('config', $config = new Repository([
            'app' => [
                'debug' => true,
                'url' => 'http://localhost',
                'timezone' => 'UTC',
                'locale' => 'en',
                'fallback_locale' => 'en',
                'key' => 'SomeRandomString',
                'cipher' => 'AES-256-CBC',
                'log' => 'daily',
                'aliases' => []
            ],
            'auth' => [
                'driver' => 'eloquent',
                'model' => User::class,
                'table' => 'users',
                'password' => [
                    'email' => 'emails.password',
                    'table' => 'password_resets',
                    'expire' => 60,
                ],
            ],
            'session' => [
                'driver' => 'file',
                'lifetime' => 120,
                'expire_on_close' => false,
                'encrypt' => false,
                'files' => storage_path('framework/sessions'),
                'connection' => null,
                'table' => 'sessions',
                'lottery' => [2, 100],
                'cookie' => 'notadd_session',
                'path' => '/',
                'domain' => null,
                'secure' => false,
            ],
            'view' => [
                'paths' => [],
                'compiled' => realpath(storage_path('framework/views')),
            ]
        ]));
        date_default_timezone_set($config['app.timezone']);
        mb_internal_encoding('UTF-8');
    }
}