<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 21:41
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
trait InjectRouterTrait {
    /**
     * @var \Illuminate\Routing\Router
     */
    private $router;
    /**
     * @return \Illuminate\Routing\Router
     */
    public function getRouter() {
        if(!isset($this->router)) {
            $this->router = Container::getInstance()->make('router');
        }
        return $this->router;
    }
}