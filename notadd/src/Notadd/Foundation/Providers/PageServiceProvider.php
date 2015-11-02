<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:29
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\AliasLoader;
class PageServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Page\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth.admin','namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->resource('page', 'PageController');
                $this->app['router']->post('page/{id}/delete', 'PageController@delete');
                $this->app['router']->post('page/{id}/restore', 'PageController@restore');
            });
            $this->app['router']->resource('page', 'PageController');
        });
        $this->loadViewsFrom($this->app->basePath() . '/resources/views/pages/', 'page');
        AliasLoader::getInstance()->alias('Page', Page::class);
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('page', function () {
            $factory = new Factory();
            return $factory;
        });
    }
}