<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:46
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectSettingTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectSettingTrait {
    /**
     * @var \Notadd\Setting\Factory
     */
    private $setting;
    /**
     * @return \Notadd\Setting\Factory
     */
    public function getSetting() {
        if(!isset($this->setting)) {
            $this->setting = Container::getInstance()->make('setting');
        }
        return $this->setting;
    }
}