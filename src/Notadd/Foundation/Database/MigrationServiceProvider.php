<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-29 00:10
 */
namespace Notadd\Foundation\Database;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;
class MigrationServiceProvider extends ServiceProvider {
    protected $defer = true;
    public function boot() {
    }
    public function register() {
        //$this->app->singleton('migration.repository', function ($app) {
        //    $table = $app['config']['database.migrations'];
        //    return new DatabaseMigrationRepository($app['db'], $table);
        //});
        //$this->app->singleton('migrator', function ($app) {
        //    $repository = $app['migration.repository'];
        //    return new Migrator($repository, $app['db'], $app['files']);
        //});
    }
}