<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:16
 */
namespace Notadd\Menu\Facades;
use Illuminate\Support\Facades\Facade;
class Menu extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'menu';
    }
}