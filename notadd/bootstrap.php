<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-16 21:38
 */
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Notadd\Foundation\Application;
use Notadd\Foundation\Console\Kernel as ConsoleKernel;
use Notadd\Foundation\Http\Kernel as HttpKernel;
use Notadd\Foundation\Install\Kernel as InstallKernel;
use Notadd\Foundation\Exceptions\Handler;
define('NOTADD_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = new Application(realpath(__DIR__));
if($app->isInstalled()) {
    $app->singleton(HttpKernelContract::class, HttpKernel::class);
    $app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
    $app->singleton(ExceptionHandler::class, Handler::class);
} else {
    $app->singleton(HttpKernelContract::class, InstallKernel::class);
    $app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
    $app->singleton(ExceptionHandler::class, Handler::class);
}
return $app;