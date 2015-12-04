<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 17:55
 */
namespace Notadd\Foundation\Database;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Seeder;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Database\Console\Seeds\SeederMakeCommand;
class SeedServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = true;
    /**
     * Register the service provider.
     * @return void
     */
    public function register() {
        $this->registerSeedCommand();
        $this->registerMakeCommand();
        $this->app->singleton('seeder', function () {
            return new Seeder;
        });
        $this->commands('command.seed', 'command.seeder.make');
    }
    /**
     * @return void
     */
    protected function registerSeedCommand() {
        $this->app->singleton('command.seed', function ($app) {
            return new SeedCommand($app['db']);
        });
    }
    /**
     * @return void
     */
    protected function registerMakeCommand() {
        $this->app->singleton('command.seeder.make', function ($app) {
            return new SeederMakeCommand($app['files'], $app['composer']);
        });
    }
    /**
     * @return array
     */
    public function provides() {
        return [
            'seeder',
            'command.seed',
            'command.seeder.make'
        ];
    }
}