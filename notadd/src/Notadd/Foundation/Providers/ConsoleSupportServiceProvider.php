<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 11:14
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\AggregateServiceProvider;
class ConsoleSupportServiceProvider extends AggregateServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @var array
     */
    protected $providers = [
        'Illuminate\Console\ScheduleServiceProvider',
        'Illuminate\Database\MigrationServiceProvider',
        'Illuminate\Database\SeedServiceProvider',
        'Illuminate\Queue\ConsoleServiceProvider',
        'Illuminate\Routing\GeneratorServiceProvider',
        'Illuminate\Session\CommandsServiceProvider',
        'Notadd\Foundation\Providers\ComposerServiceProvider',
    ];
}