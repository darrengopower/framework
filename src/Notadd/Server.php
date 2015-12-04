<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd;
use Illuminate\Http\Request;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Notadd\Foundation\Application;
use Notadd\Foundation\Console\ConsoleServiceProvider;
use Notadd\Foundation\Console\ConsoleSupportServiceProvider;
use Notadd\Foundation\Console\Kernel as ConsoleKernel;
use Notadd\Foundation\Http\Kernel as HttpKernel;
use Notadd\Foundation\Install\Kernel as InstallKernel;
use Notadd\Foundation\Exceptions\Handler;
use Notadd\Install\InstallServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
class Server {
    private $app;
    private $path;
    public function __construct($path) {
        define('NOTADD_START', microtime(true));
        $this->path = realpath($path);
    }
    public function init() {
        $this->app = new Application($this->path);
        if($this->app->isInstalled()) {
            $this->app->singleton(HttpKernelContract::class, HttpKernel::class);
            $this->app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
            $this->app->singleton(ExceptionHandler::class, Handler::class);
        } else {
            $this->app->singleton(HttpKernelContract::class, InstallKernel::class);
            $this->app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
            $this->app->singleton(ExceptionHandler::class, Handler::class);
        }
        return $this;
    }
    public function terminate() {
        $kernel = $this->app->make(HttpKernelContract::class);
        $response = $kernel->handle(
            $request = Request::capture()
        );
        $response->send();
        $kernel->terminate($request, $response);
    }
    public function console() {
        $this->app = new Application($this->path);
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ConsoleSupportServiceProvider::class);
        if(!$this->app->isInstalled()) {
            $this->app->register(InstallServiceProvider::class);
        }
        $this->app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
        $this->app->singleton(ExceptionHandler::class, Handler::class);
        $kernel = $this->app->make(ConsoleKernelContract::class);
        $status = $kernel->handle(
            $input = new ArgvInput,
            new ConsoleOutput
        );
        $kernel->terminate($input, $status);
        exit($status);
    }
}