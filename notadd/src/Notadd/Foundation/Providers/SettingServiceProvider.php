<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\AliasLoader;
use Notadd\Setting\Facades\Setting;
use Notadd\Setting\Factory;
class SettingServiceProvider extends ServiceProvider {
    /**
     * @return array
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Setting\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->get('site', 'ConfigController@getSite');
                $this->app['router']->post('site', 'ConfigController@postSite');
                $this->app['router']->get('seo', 'ConfigController@getSeo');
                $this->app['router']->post('seo', 'ConfigController@postSeo');
            });
        });
        AliasLoader::getInstance()->alias('Setting', Setting::class);
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
            $factory = new Factory();
            return $factory;
        });
    }
}