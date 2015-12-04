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
use Illuminate\Contracts\Config\Repository as RepositoryContract;
class LoadConfiguration {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $app->instance('config', $config = new Repository([]));
        $this->loadConfiguration($app, $config);
        date_default_timezone_set($config['app.timezone']);
        mb_internal_encoding('UTF-8');
    }
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @return void
     */
    protected function loadConfiguration(Application $app, RepositoryContract $config) {
        $config->set('app', [
            'debug' => true,
            'url' => 'http://localhost',
            'timezone' => 'UTC',
            'locale' => 'en',
            'fallback_locale' => 'en',
            'key' => 'SomeRandomString',
            'cipher' => 'AES-256-CBC',
            'log' => 'daily',
            'aliases' => []
        ]);
        $config->set('view', [
            'paths' => [],
            'compiled' => realpath(storage_path('framework/views')),
        ]);
        $config->set('session.driver', 'file');
        $config->set('session.lifetime', 120);
        $config->set('session.expire_on_close', false);
        $config->set('session.encrypt', false);
        $config->set('session.files', storage_path('framework/sessions'));
        $config->set('session.connection', null);
        $config->set('session.table', 'sessions');
        $config->set('session.lottery', [2, 100]);
        $config->set('session.cookie', 'notadd_session');
        $config->set('session.path', '/');
        $config->set('session.domain', null);
        $config->set('session.secure', false);
    }
}