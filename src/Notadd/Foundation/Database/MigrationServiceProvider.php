<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-29 00:10
 */
namespace Notadd\Foundation\Database;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Database\Console\Migrations\InstallCommand;
use Notadd\Foundation\Database\Console\Migrations\MigrateCommand;
use Notadd\Foundation\Database\Console\Migrations\MigrateMakeCommand;
use Notadd\Foundation\Database\Console\Migrations\RefreshCommand;
use Notadd\Foundation\Database\Console\Migrations\ResetCommand;
use Notadd\Foundation\Database\Console\Migrations\RollbackCommand;
use Notadd\Foundation\Database\Console\Migrations\StatusCommand;
use Notadd\Foundation\Database\Migrations\DatabaseMigrationRepository;
use Notadd\Foundation\Database\Migrations\MigrationCreator;
use Notadd\Foundation\Database\Migrations\Migrator;
/**
 * Class MigrationServiceProvider
 * @package Notadd\Foundation\Database
 */
class MigrationServiceProvider extends ServiceProvider {
    protected $defer = true;
    public function boot() {
    }
    public function register() {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];
            return new DatabaseMigrationRepository($app['db'], $table);
        });
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];
            return new Migrator($app, $repository, $app['db'], $app['files']);
        });
        $commands = [
            'Migrate',
            'Rollback',
            'Reset',
            'Refresh',
            'Install',
            'Make',
            'Status'
        ];
        foreach($commands as $command) {
            $this->{'register' . $command . 'Command'}();
        }
        $this->commands('command.migrate', 'command.migrate.make', 'command.migrate.install', 'command.migrate.rollback', 'command.migrate.reset', 'command.migrate.refresh', 'command.migrate.status');
    }
    /**
     * @return void
     */
    protected function registerMigrateCommand() {
        $this->app->singleton('command.migrate', function ($app) {
            return new MigrateCommand($app['migrator']);
        });
    }
    /**
     * @return void
     */
    protected function registerRollbackCommand() {
        $this->app->singleton('command.migrate.rollback', function ($app) {
            return new RollbackCommand($app['migrator']);
        });
    }
    /**
     * @return void
     */
    protected function registerResetCommand() {
        $this->app->singleton('command.migrate.reset', function ($app) {
            return new ResetCommand($app['migrator']);
        });
    }
    /**
     * @return void
     */
    protected function registerRefreshCommand() {
        $this->app->singleton('command.migrate.refresh', function () {
            return new RefreshCommand;
        });
    }
    /**
     * @return void
     */
    protected function registerStatusCommand() {
        $this->app->singleton('command.migrate.status', function ($app) {
            return new StatusCommand($app['migrator']);
        });
    }
    /**
     * @return void
     */
    protected function registerInstallCommand() {
        $this->app->singleton('command.migrate.install', function ($app) {
            return new InstallCommand($app['migration.repository']);
        });
    }
    /**
     * @return void
     */
    protected function registerMakeCommand() {
        $this->registerCreator();
        $this->app->singleton('command.migrate.make', function ($app) {
            $creator = $app['migration.creator'];
            $composer = $app['composer'];
            return new MigrateMakeCommand($creator, $composer);
        });
    }
    /**
     * @return void
     */
    protected function registerCreator() {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app, $app['files']);
        });
    }
    /**
     * @return array
     */
    public function provides() {
        return [
            'migrator',
            'migration.repository',
            'command.migrate',
            'command.migrate.rollback',
            'command.migrate.reset',
            'command.migrate.refresh',
            'command.migrate.install',
            'command.migrate.status',
            'migration.creator',
            'command.migrate.make',
        ];
    }
}