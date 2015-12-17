<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 20:00
 */
namespace Notadd\Foundation\Cache;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Cache\Console\CacheTableCommand;
class CacheServiceProvider extends ServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('cache', function ($app) {
            return new CacheManager($app);
        });
        $this->app->singleton('cache.store', function ($app) {
            return $app['cache']->driver();
        });
        $this->app->singleton('memcached.connector', function () {
            return new MemcachedConnector;
        });
        $this->registerCommands();
    }
    /**
     * @return void
     */
    public function registerCommands() {
        $this->app->singleton('command.cache.clear', function ($app) {
            return new ClearCommand($app['cache']);
        });
        $this->app->singleton('command.cache.table', function ($app) {
            return new CacheTableCommand($app['files'], $app['composer']);
        });
        $this->commands('command.cache.clear', 'command.cache.table');
    }
    /**
     * @return array
     */
    public function provides() {
        return [
            'cache',
            'cache.store',
            'memcached.connector',
            'command.cache.clear',
            'command.cache.table',
        ];
    }
}