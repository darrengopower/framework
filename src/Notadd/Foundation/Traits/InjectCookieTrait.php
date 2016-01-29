<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-17 21:48
 */
namespace Notadd\Foundation\Traits;
use Illuminate\Container\Container;
/**
 * Class InjectCookieTrait
 * @package Notadd\Foundation\Traits
 */
trait InjectCookieTrait {
    /**
     * @var \Illuminate\Cookie\CookieJar
     */
    private $cookie;
    /**
     * @return \Illuminate\Cookie\CookieJar
     */
    public function getCookie() {
        if(!isset($this->cookie)) {
            $this->cookie = Container::getInstance()->make('cookie');
        }
        return $this->cookie;
    }
}