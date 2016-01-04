<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:08
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectBladeTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectBladeTrait {
    /**
     * @var \Illuminate\View\Compilers\BladeCompiler
     */
    private $blade;
    /**
     * @return \Illuminate\View\Compilers\BladeCompiler
     */
    public function getBlade() {
        if(!isset($this->blade)) {
            $this->blade = Container::getInstance()->make('view')->getEngineResolver()->resolve('blade')->getCompiler();
        }
        return $this->blade;
    }
}