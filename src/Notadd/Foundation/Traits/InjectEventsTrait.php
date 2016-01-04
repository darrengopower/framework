<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:17
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectEventsTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectEventsTrait {
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $events;
    /**
     * @return \Illuminate\Events\Dispatcher
     */
    public function getEvents() {
        if(!isset($this->events)) {
            $this->events = Container::getInstance()->make('events');
        }
        return $this->events;
    }
}