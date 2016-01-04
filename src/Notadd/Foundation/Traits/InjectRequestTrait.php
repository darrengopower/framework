<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 21:52
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectRequestTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectRequestTrait {
    /**
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * @return \Illuminate\Http\Request
     */
    public function getRequest() {
        if(!isset($this->request)) {
            $this->request = Container::getInstance()->make('request');
        }
        return $this->request;
    }
}