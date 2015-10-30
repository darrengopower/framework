<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Install;
use Notadd\Foundation\Bootstrap\BootProviders;
use Notadd\Foundation\Bootstrap\ConfigureLogging;
use Notadd\Foundation\Bootstrap\DetectEnvironment;
use Notadd\Foundation\Bootstrap\HandleExceptions;
use Notadd\Foundation\Bootstrap\RegisterFacades;
use Notadd\Foundation\Http\Kernel as HttpKernel;
use Notadd\Foundation\Install\Bootstrap\LoadConfiguration;
use Notadd\Foundation\Install\Bootstrap\RegisterProviders;
class Kernel extends HttpKernel {
    /**
     * @var array
     */
    protected $bootstrappers = [
        DetectEnvironment::class,
        LoadConfiguration::class,
        ConfigureLogging::class,
        HandleExceptions::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];
    /**
     * @var array
     */
    protected $middleware = [];
    /**
     * @var array
     */
    protected $routeMiddleware = [];
}