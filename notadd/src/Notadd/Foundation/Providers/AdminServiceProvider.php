<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 18:19
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
class AdminServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom(realpath($this->app->basePath() . '/../template/admin/views'), 'admin');
        $this->app['router']->group(['namespace' => 'Notadd\Admin\Controllers'], function () {
            $this->app['router']->group(['prefix' => 'admin'], function () {
                $this->app['router']->get('login', 'AuthController@getLogin');
                $this->app['router']->post('login', 'AuthController@postLogin');
                $this->app['router']->get('logout', 'AuthController@getLogout');
                $this->app['router']->get('register', 'AuthController@getRegister');
                $this->app['router']->post('register', 'AuthController@postRegister');
            });
            $this->app['router']->group(['middleware' => 'auth.admin', 'prefix' => 'admin'], function () {
                $this->app['router']->get('/', 'AdminController@init');
            });
        });
        if($this->app['request']->is('admin*')) {
            $menu = $this->app['config']->get('admin');
            foreach($menu as $top_key => $top) {
                if(isset($top['sub'])) {
                    foreach($top['sub'] as $one_key => $one) {
                        if(isset($one['sub'])) {
                            $active = false;
                            foreach((array)$one['active'] as $rule) {
                                if($this->app['request']->is($rule)) {
                                    $active = true;
                                }
                            }
                            if($active) {
                                $menu[$top_key]['sub'][$one_key]['active'] = 'open';
                            } else {
                                $menu[$top_key]['sub'][$one_key]['active'] = '';
                            }
                        } else {
                            if($this->app['request']->is($one['active'])) {
                                $menu[$top_key]['sub'][$one_key]['active'] = 'active';
                            } else {
                                $menu[$top_key]['sub'][$one_key]['active'] = '';
                            }
                        }
                    }
                }
            }
            $this->app['config']->set('admin', $menu);
        }
    }
    /**
     * @return void
     */
    public function register() {
    }
}