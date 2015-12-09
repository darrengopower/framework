<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 14:51
 */
namespace Notadd\Foundation\SearchEngine;
use Illuminate\Support\ServiceProvider;
class SearchEngineServiceProvider extends ServiceProvider {
    public function boot() {
    }
    public function register() {
        $this->app->singleton('searchengine.optimization', Optimization::class);
    }
}