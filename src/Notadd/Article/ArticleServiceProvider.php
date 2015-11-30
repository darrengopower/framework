<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:03
 */
namespace Notadd\Article;
use Illuminate\Support\ServiceProvider;
class ArticleServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app->make('router')->group(['namespace' => 'Notadd\Article\Controllers'], function () {
            $this->app->make('router')->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app->make('router')->resource('article', 'ArticleController');
                $this->app->make('router')->post('article/{id}/delete', 'ArticleController@delete');
                $this->app->make('router')->post('article/{id}/restore', 'ArticleController@restore');
                $this->app->make('router')->post('article/select', 'ArticleController@select');
            });
            $this->app->make('router')->resource('article', 'ArticleController');
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}