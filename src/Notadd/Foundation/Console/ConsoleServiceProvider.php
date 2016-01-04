<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 00:12
 */
namespace Notadd\Foundation\Console;
use Illuminate\Support\ServiceProvider;
/**
 * Class ConsoleServiceProvider
 * @package Notadd\Foundation\Console
 */
class ConsoleServiceProvider extends ServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @var array
     */
    protected $commands = [
        'ClearCompiled' => 'command.clear-compiled',
        'ConfigCache' => 'command.config.cache',
        'ConfigClear' => 'command.config.clear',
        'Down' => 'command.down',
        'RouteCache' => 'command.route.cache',
        'RouteClear' => 'command.route.clear',
        'RouteList' => 'command.route.list',
        'Up' => 'command.up',
        'VendorPublish' => 'command.vendor.publish',
        'ViewClear' => 'command.view.clear',
    ];
    /**
     * @return void
     */
    public function register() {
        foreach(array_keys($this->commands) as $command) {
            $method = "register{$command}Command";
            call_user_func_array([
                $this,
                $method
            ], []);
        }
        $this->commands(array_values($this->commands));
    }
    /**
     * @return void
     */
    protected function registerClearCompiledCommand() {
        $this->app->singleton('command.clear-compiled', function () {
            return new ClearCompiledCommand;
        });
    }
    /**
     * @return void
     */
    protected function registerConfigCacheCommand() {
        $this->app->singleton('command.config.cache', function ($app) {
            return new ConfigCacheCommand($app['files']);
        });
    }
    /**
     * @return void
     */
    protected function registerConfigClearCommand() {
        $this->app->singleton('command.config.clear', function ($app) {
            return new ConfigClearCommand($app['files']);
        });
    }
    /**
     * @return void
     */
    protected function registerDownCommand() {
        $this->app->singleton('command.down', function () {
            return new DownCommand;
        });
    }
    /**
     * @return void
     */
    protected function registerRouteCacheCommand() {
        $this->app->singleton('command.route.cache', function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }
    /**
     * @return void
     */
    protected function registerRouteClearCommand() {
        $this->app->singleton('command.route.clear', function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }
    /**
     * @return void
     */
    protected function registerRouteListCommand() {
        $this->app->singleton('command.route.list', function ($app) {
            return new RouteListCommand($app['router']);
        });
    }
    /**
     * @return void
     */
    protected function registerUpCommand() {
        $this->app->singleton('command.up', function () {
            return new UpCommand;
        });
    }
    /**
     * @return void
     */
    protected function registerVendorPublishCommand() {
        $this->app->singleton('command.vendor.publish', function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }
    /**
     * @return void
     */
    protected function registerViewClearCommand() {
        $this->app->singleton('command.view.clear', function ($app) {
            return new ViewClearCommand($app['files']);
        });
    }
}