<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 00:07
 */
namespace Notadd\Foundation\Console;
use Illuminate\Support\AggregateServiceProvider;
use Notadd\Foundation\Composer\ComposerServiceProvider;
use Notadd\Foundation\Database\MigrationServiceProvider;
use Notadd\Foundation\Database\SeedServiceProvider;
use Notadd\Foundation\Queue\ConsoleServiceProvider as QueueConsoleServiceProvider;
use Notadd\Foundation\Session\ConsoleServiceProvider as SessionConsoleServiceProvider;
class ConsoleSupportServiceProvider extends AggregateServiceProvider {
    protected $defer = true;
    protected $providers = [
        'Illuminate\Auth\GeneratorServiceProvider',
        'Illuminate\Console\ScheduleServiceProvider',
        ComposerServiceProvider::class,
        MigrationServiceProvider::class,
        SeedServiceProvider::class,
        QueueConsoleServiceProvider::class,
        'Illuminate\Routing\GeneratorServiceProvider',
        SessionConsoleServiceProvider::class,
    ];
}