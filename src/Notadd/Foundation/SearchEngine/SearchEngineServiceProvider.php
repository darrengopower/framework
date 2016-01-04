<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 14:51
 */
namespace Notadd\Foundation\SearchEngine;
use Illuminate\Support\ServiceProvider;
/**
 * Class SearchEngineServiceProvider
 * @package Notadd\Foundation\SearchEngine
 */
class SearchEngineServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton(Optimization::class, function() {
            return new Optimization($this->app, $this->app->make('setting'), $this->app->make('view'));
        });
    }
}