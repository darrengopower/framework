<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:46
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
class CategoryServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Category\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->resource('category', 'CategoryController');
                $this->app['router']->post('category/{id}/status', 'CategoryController@status');
            });
            $this->app['router']->resource('category', 'CategoryController');
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}