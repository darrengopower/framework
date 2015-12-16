<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Routing;
use BadMethodCallException;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Str;
use Notadd\Foundation\Bus\DispatchesJobs;
use Notadd\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
abstract class Controller extends IlluminateController {
    use DispatchesJobs, ValidatesRequests;
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Application $app, GateContract $gate, Factory $view) {
        $this->app = $app;
        $this->gate = $gate;
        $this->view = $view;
    }
    public function getCommand($command) {
        return $this->app->make('Illuminate\Contracts\Console\Kernel')->find($command);
    }
    /**
     * @param array $parameters
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function missingMethod($parameters = []) {
        throw new NotFoundHttpException('控制器方法未找到。');
    }
    /**
     * @param $key
     * @param null $value
     */
    protected function share($key, $value = null) {
        $this->view->share($key, $value);
    }
    /**
     * @param $template
     * @return \Illuminate\Contracts\View\View
     */
    protected function view($template) {
        if(Str::contains($template, '::')) {
            return $this->view->make($template);
        } else {
            return $this->view->make('themes::' . $template);
        }
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters) {
        throw new BadMethodCallException("方法[$method]不存在。");
    }
}