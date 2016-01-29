<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:44
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectViewTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectViewTrait {
    /**
     * @var \Illuminate\View\Factory
     */
    private $view;
    /**
     * @return \Illuminate\View\Factory
     */
    public function getView() {
        if(!isset($this->view)) {
            $this->view = Container::getInstance()->make('view');
        }
        return $this->view;
    }
}