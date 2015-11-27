<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:30
 */
namespace Notadd\Page\Facades;
use Illuminate\Support\Facades\Facade;
class Page extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'page';
    }
}