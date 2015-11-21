<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 14:41
 */
namespace Notadd\Foundation\SearchEngine\Facades;
use Illuminate\Support\Facades\Facade;
class SearchEngineOptimization extends Facade {
    protected static function getFacadeAccessor() {
        return 'searchengine.optimization';
    }
}