<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation;
use Illuminate\Http\Request;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
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
    private $application;
    private $path;
    public function __construct($path) {
        define('NOTADD_START', microtime(true));
        $this->path = realpath($path);
    }
    public function init() {
        $this->application = new Application($this->path);
        $this->application->detectEnvironment(function () {
            return 'production';
        });
        $this->application->registerConfiguredProviders();
        if($this->application->isInstalled()) {
            $this->application->singleton(HttpKernelContract::class, HttpKernel::class);
            $this->application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
            $this->application->singleton(ExceptionHandler::class, Handler::class);
        } else {
            $this->application->singleton(HttpKernelContract::class, InstallKernel::class);
            $this->application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
            $this->application->singleton(ExceptionHandler::class, Handler::class);
        }
        return $this;
    }
    public function terminate() {
        $kernel = $this->application->make(HttpKernelContract::class);
        $response = $kernel->handle(
            $request = Request::capture()
        );
        $response->send();
        $kernel->terminate($request, $response);
    }
    public function console() {
        $this->application = new Application($this->path);
        $this->application->register(ConsoleServiceProvider::class);
        $this->application->register(ConsoleSupportServiceProvider::class);
        if(!$this->application->isInstalled()) {
            $this->application->register(InstallServiceProvider::class);
        }
        $this->application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
        $this->application->singleton(ExceptionHandler::class, Handler::class);
        $kernel = $this->application->make(ConsoleKernelContract::class);
        $status = $kernel->handle(
            $input = new ArgvInput,
            new ConsoleOutput
        );
        $kernel->terminate($input, $status);
        exit($status);
    }
}