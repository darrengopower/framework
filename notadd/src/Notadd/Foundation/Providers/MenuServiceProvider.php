<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:02
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\AliasLoader;
use Notadd\Menu\Models\Menu;
class MenuServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Menu\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->resource('menu', 'GroupController');
                $this->app['router']->get('menu/{id}/sort', 'GroupController@sort');
                $this->app['router']->post('menu/{id}/sorting', 'GroupController@sorting');
                $this->app['router']->resource('menu/item', 'ItemController');
                $this->app['router']->post('menu/item/{id}/status', 'ItemController@status');
                $this->app['router']->get('menu/item/{id}/sort', 'ItemController@sort');
                $this->app['router']->post('menu/item/{id}/sorting', 'ItemController@sorting');
            });
        });
        AliasLoader::getInstance()->alias('Menu', Menu::class);
    }
    /**
     * @return array
     */
    public function provides() {
        return ['menu'];
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('menu', function ($app) {
            $factory = new Factory($app->make('request'));
            return $factory;
        });
    }
}