<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:03
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
class ArticleServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Article\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->resource('article', 'ArticleController');
                $this->app['router']->post('article/{id}/delete', 'ArticleController@delete');
                $this->app['router']->post('article/{id}/restore', 'ArticleController@restore');
                $this->app['router']->post('article/select', 'ArticleController@select');
            });
            $this->app['router']->resource('article', 'ArticleController');
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}