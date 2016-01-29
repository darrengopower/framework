<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:33
 */
namespace Notadd\Foundation\Database;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Database\Connectors\ConnectionFactory;
use Notadd\Foundation\Database\Eloquent\Factory as EloquentFactory;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Eloquent\QueueEntityResolver;
/**
 * Class DatabaseServiceProvider
 * @package Notadd\Foundation\Database
 */
class DatabaseServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }
    /**
     * @return void
     */
    public function register() {
        Model::clearBootedModels();
        $this->registerEloquentFactory();
        $this->registerQueueableEntityResolver();
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }
    /**
     * @return void
     */
    protected function registerEloquentFactory() {
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create();
        });
        $this->app->singleton(EloquentFactory::class, function ($app) {
            $faker = $app->make(FakerGenerator::class);
            return EloquentFactory::construct($faker, database_path('factories'));
        });
    }
    /**
     * @return void
     */
    protected function registerQueueableEntityResolver() {
        $this->app->singleton('Illuminate\Contracts\Queue\EntityResolver', function () {
            return new QueueEntityResolver;
        });
    }
}