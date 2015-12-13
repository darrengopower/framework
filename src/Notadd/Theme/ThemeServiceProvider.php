<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:31
 */
namespace Notadd\Theme;
use Illuminate\Support\ServiceProvider;
class ThemeServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app->make('router')->group(['namespace' => 'Notadd\Theme\Controllers'], function () {
            $this->app->make('router')->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app->make('router')->post('theme/cookie', function() {
                    $default = $this->app->make('request')->input('theme');
                    $this->app->make('cookie')->queue($this->app->make('cookie')->forever('admin-theme', $default));
                });
                $this->app->make('router')->resource('theme', 'ThemeController');
            });
        });
        $default = $this->app->make('setting')->get('site.theme', 'default');
        $this->app->make('events')->listen('router.matched', function () use ($default) {
            $list = $this->app->make('theme')->getThemeList();
            foreach($list as $theme) {
                $alias = $theme->getAlias();
                if($alias == $default) {
                    $this->loadViewsFrom($theme->getViewPath(), 'themes');
                }
                $this->loadViewsFrom($theme->getViewPath(), $alias);
                $this->publishes([
                    $theme->getCssPath() => $theme->getCssStaticPath(),
                    $theme->getFontPath() => $theme->getFontStaticPath(),
                    $theme->getJsPath() => $theme->getJsStaticPath(),
                    $theme->getImagePath() => $theme->getImageStaticPath(),
                ], $alias);
            }
        });
        $this->app->make('events')->listen('kernel.handled', function () use ($default) {
            $this->app->make('theme')->publishAssets();
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