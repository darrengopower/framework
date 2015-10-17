<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-16 21:44
 */
namespace Notadd\Foundation\Http;
use Exception;
use Throwable;
use Illuminate\Routing\Router;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as KernelContract;
use Symfony\Component\Debug\Exception\FatalThrowableError;
class Kernel implements KernelContract {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;
    /**
     * @var array
     */
    protected $bootstrappers = [
        'Notadd\Foundation\Bootstrap\DetectEnvironment',
        'Notadd\Foundation\Bootstrap\LoadConfiguration',
        'Notadd\Foundation\Bootstrap\ConfigureLogging',
        'Notadd\Foundation\Bootstrap\HandleExceptions',
        'Notadd\Foundation\Bootstrap\RegisterFacades',
        'Notadd\Foundation\Bootstrap\RegisterProviders',
        'Notadd\Foundation\Bootstrap\BootProviders',
    ];
    /**
     * @var array
     */
    protected $middleware = [];
    /**
     * @var array
     */
    protected $routeMiddleware = [];
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function __construct(Application $app, Router $router) {
        $this->app = $app;
        $this->router = $router;
        foreach($this->routeMiddleware as $key => $middleware) {
            $router->middleware($key, $middleware);
        }
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle($request) {
        try {
            $request->enableHttpMethodParameterOverride();
            $response = $this->sendRequestThroughRouter($request);
        } catch(Exception $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } catch(Throwable $e) {
            $e = new FatalThrowableError($e);
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }
        $this->app['events']->fire('kernel.handled', [
            $request,
            $response
        ]);
        return $response;
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function sendRequestThroughRouter($request) {
        $this->app->instance('request', $request);
        Facade::clearResolvedInstance('request');
        $this->bootstrap();
        return (new Pipeline($this->app))->send($request)->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)->then($this->dispatchToRouter());
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\Response $response
     * @return void
     */
    public function terminate($request, $response) {
        $middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge($this->gatherRouteMiddlewares($request), $this->middleware);
        foreach($middlewares as $middleware) {
            list($name, $parameters) = $this->parseMiddleware($middleware);
            $instance = $this->app->make($name);
            if(method_exists($instance, 'terminate')) {
                $instance->terminate($request, $response);
            }
        }
        $this->app->terminate();
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function gatherRouteMiddlewares($request) {
        if($request->route()) {
            return $this->router->gatherRouteMiddlewares($request->route());
        }
        return [];
    }
    /**
     * @param  string $middleware
     * @return array
     */
    protected function parseMiddleware($middleware) {
        list($name, $parameters) = array_pad(explode(':', $middleware, 2), 2, []);
        if(is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }
        return [
            $name,
            $parameters
        ];
    }
    /**
     * @param  string $middleware
     * @return $this
     */
    public function prependMiddleware($middleware) {
        if(array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }
        return $this;
    }
    /**
     * @param  string $middleware
     * @return $this
     */
    public function pushMiddleware($middleware) {
        if(array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }
        return $this;
    }
    /**
     * @return void
     */
    public function bootstrap() {
        if(!$this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }
    /**
     * @return \Closure
     */
    protected function dispatchToRouter() {
        return function ($request) {
            $this->app->instance('request', $request);
            return $this->router->dispatch($request);
        };
    }
    /**
     * @param  string $middleware
     * @return bool
     */
    public function hasMiddleware($middleware) {
        return array_key_exists($middleware, array_flip($this->middleware));
    }
    /**
     * @return array
     */
    protected function bootstrappers() {
        return $this->bootstrappers;
    }
    /**
     * @param  \Exception $e
     * @return void
     */
    protected function reportException(Exception $e) {
        $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->report($e);
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Exception $e) {
        return $this->app['Illuminate\Contracts\Debug\ExceptionHandler']->render($request, $e);
    }
    /**
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication() {
        return $this->app;
    }
}