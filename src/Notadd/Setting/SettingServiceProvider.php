<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:11
 */
namespace Notadd\Setting;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\AliasLoader;
class SettingServiceProvider extends ServiceProvider {
    /**
     * @return array
     */
    public function boot() {
        $this->app->make('router')->group(['namespace' => 'Notadd\Setting\Controllers'], function () {
            $this->app->make('router')->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app->make('router')->get('site', 'ConfigController@getSite');
                $this->app->make('router')->post('site', 'ConfigController@postSite');
                $this->app->make('router')->get('seo', 'ConfigController@getSeo');
                $this->app->make('router')->post('seo', 'ConfigController@postSeo');
            });
        });
        AliasLoader::getInstance()->alias('Setting', $this->app->make('setting'));
    }
    /**
     * @return array
     */
    public function provides() {
        return ['setting'];
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('setting', function () {
            return $this->app->make(Factory::class);
        });
    }
}