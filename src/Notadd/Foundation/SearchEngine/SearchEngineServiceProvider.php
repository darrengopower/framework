<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 14:51
 */
namespace Notadd\Foundation\SearchEngine;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\AliasLoader;
use Notadd\Foundation\SearchEngine\Facades\SearchEngineOptimization;
use Notadd\Foundation\SearchEngine\Optimization;
class SearchEngineServiceProvider extends ServiceProvider {
    public function boot() {
        AliasLoader::getInstance()->alias('Seo', SearchEngineOptimization::class);
    }
    public function register() {
        $this->app->singleton('searchengine.optimization', Optimization::class);
    }
}