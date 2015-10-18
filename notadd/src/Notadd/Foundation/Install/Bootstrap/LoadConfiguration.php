<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Install\Bootstrap;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Routing\ControllerServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Notadd\Foundation\Providers\ExtensionServiceProvider;
use Notadd\Foundation\Providers\FormRequestServiceProvider;
use Notadd\Install\InstallServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
class LoadConfiguration {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $items = [];
        $app->instance('config', $config = new Repository($items));
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
            'providers' => [
                CacheServiceProvider::class,
                ControllerServiceProvider::class,
                CookieServiceProvider::class,
                DatabaseServiceProvider::class,
                EncryptionServiceProvider::class,
                FilesystemServiceProvider::class,
                HashServiceProvider::class,
                MailServiceProvider::class,
                PaginationServiceProvider::class,
                PipelineServiceProvider::class,
                QueueServiceProvider::class,
                RedisServiceProvider::class,
                SessionServiceProvider::class,
                TranslationServiceProvider::class,
                ValidationServiceProvider::class,
                ViewServiceProvider::class,
                FormRequestServiceProvider::class,
                ExtensionServiceProvider::class,
                InstallServiceProvider::class
            ],
            'aliases' => []
        ]);
        $config->set('view', [
            'paths' => [
                realpath(base_path('resources/views')),
            ],
            'compiled' => realpath(storage_path('framework/views')),
        ]);
    }
}