<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 22:57
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
trait InjectConfigTrait {
    /**
     * @var \Illuminate\Config\Repository
     */
    private $config;
    /**
     * @return \Illuminate\Config\Repository
     */
    public function getConfig() {
        if(!isset($this->config)) {
            $this->config = Container::getInstance()->make('config');
        }
        return $this->config;
    }
}