<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:33
 */
namespace Notadd\Theme\Facades;
use Illuminate\Support\Facades\Facade;
class Theme extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'theme';
    }
}