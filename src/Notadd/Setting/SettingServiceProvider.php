<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:11
 */
namespace Notadd\Setting;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Traits\InjectRouterTrait;
class SettingServiceProvider extends ServiceProvider {
    use InjectRouterTrait;
    /**
     * @return array
     */
    public function boot() {
        $this->getRouter()->group(['namespace' => 'Notadd\Setting\Controllers'], function () {
            $this->getRouter()->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->getRouter()->get('site', 'ConfigController@getSite');
                $this->getRouter()->post('site', 'ConfigController@postSite');
                $this->getRouter()->get('seo', 'ConfigController@getSeo');
                $this->getRouter()->post('seo', 'ConfigController@postSeo');
            });
        });
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