<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 18:09
 */
namespace Notadd\Foundation\Queue;
use Illuminate\Queue\Console\FlushFailedCommand;
use Illuminate\Queue\Console\ForgetFailedCommand;
use Illuminate\Queue\Console\ListFailedCommand;
use Illuminate\Queue\Console\RetryCommand;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Queue\Console\FailedTableCommand;
use Notadd\Foundation\Queue\Console\TableCommand;
/**
 * Class ConsoleServiceProvider
 * @package Notadd\Foundation\Queue
 */
class ConsoleServiceProvider extends ServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('command.queue.table', function ($app) {
            return new TableCommand($app['files'], $app['composer']);
        });
        $this->app->singleton('command.queue.failed', function () {
            return new ListFailedCommand;
        });
        $this->app->singleton('command.queue.retry', function () {
            return new RetryCommand;
        });
        $this->app->singleton('command.queue.forget', function () {
            return new ForgetFailedCommand;
        });
        $this->app->singleton('command.queue.flush', function () {
            return new FlushFailedCommand;
        });
        $this->app->singleton('command.queue.failed-table', function ($app) {
            return new FailedTableCommand($app['files'], $app['composer']);
        });
        $this->commands('command.queue.table', 'command.queue.failed', 'command.queue.retry', 'command.queue.forget', 'command.queue.flush', 'command.queue.failed-table');
    }
    /**
     * @return array
     */
    public function provides() {
        return [
            'command.queue.table',
            'command.queue.failed',
            'command.queue.retry',
            'command.queue.forget',
            'command.queue.flush',
            'command.queue.failed-table',
        ];
    }
}