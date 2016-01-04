<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:40
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectThemeTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectThemeTrait {
    /**
     * @var \Notadd\Theme\Factory
     */
    private $theme;
    /**
     * @return \Notadd\Theme\Factory
     */
    public function getTheme() {
        if(!isset($this->theme)) {
            $this->theme = Container::getInstance()->make('theme');
        }
        return $this->theme;
    }
}