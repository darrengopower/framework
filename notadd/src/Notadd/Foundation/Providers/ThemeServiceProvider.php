<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:31
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Theme\Factory;
class ThemeServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->group(['namespace' => 'Notadd\Theme\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->post('theme/cookie', function() {
                    $default = $this->app['request']->input('theme');
                    $this->app['cookie']->queue($this->app['cookie']->forever('admin-theme', $default));
                });
                $this->app['router']->resource('theme', 'ThemeController');
            });
        });
        $default = $this->app['setting']->get('site.theme');
        $this->app['events']->listen('router.matched', function () use ($default) {
            foreach($this->app['theme']->getThemeList() as $theme) {
                $alias = $theme->getAlias();
                if($alias == $default) {
                    $this->loadViewsFrom($theme->getViewPath(), 'themes');
                }
                $this->loadViewsFrom($theme->getViewPath(), $alias);
                $this->publishes([
                    $theme->getCssPath() => public_path('themes/' . $alias . '/css'),
                    $theme->getFontPath() => public_path('themes/' . $alias . '/fonts'),
                    $theme->getJsPath() => public_path('themes/' . $alias . '/js'),
                    $theme->getImagePath() => public_path('themes/' . $alias . '/images'),
                ], $alias);
            }
        });
        $this->app['events']->listen('kernel.handled', function () use ($default) {
            $this->app['theme']->publishAssets();
        });
    }
    /**
     * @return array
     */
    public function provides() {
        return ['theme'];
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('theme', function () {
            return $this->app->make(Factory::class);
        });
    }
}