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
        $this->initAdminConfig();
        $this->loadViewsFrom(realpath($this->app->basePath() . '/../template/admin/views'), 'admin');
        $this->app->make('router')->group(['namespace' => 'Notadd\Admin\Controllers'], function () {
            $this->app->make('router')->group(['prefix' => 'admin'], function () {
                $this->app->make('router')->get('login', 'AuthController@getLogin');
                $this->app->make('router')->post('login', 'AuthController@postLogin');
                $this->app->make('router')->get('logout', 'AuthController@getLogout');
                $this->app->make('router')->get('register', 'AuthController@getRegister');
                $this->app->make('router')->post('register', 'AuthController@postRegister');
                $this->app['router']->controllers(['password' => 'PasswordController']);
            });
            $this->app->make('router')->group(['middleware' => 'auth.admin', 'prefix' => 'admin'], function () {
                $this->app->make('router')->get('/', 'AdminController@init');
            });
        });
        if($this->app->make('request')->is('admin*')) {
            $menu = $this->app->make('config')->get('admin');
            foreach($menu as $top_key => $top) {
                if(isset($top['sub'])) {
                    foreach($top['sub'] as $one_key => $one) {
                        if(isset($one['sub'])) {
                            $active = false;
                            foreach((array)$one['active'] as $rule) {
                                if($this->app->make('request')->is($rule)) {
                                    $active = true;
                                }
                            }
                            if($active) {
                                $menu[$top_key]['sub'][$one_key]['active'] = 'open';
                            } else {
                                $menu[$top_key]['sub'][$one_key]['active'] = '';
                            }
                        } else {
                            if($this->app->make('request')->is($one['active'])) {
                                $menu[$top_key]['sub'][$one_key]['active'] = 'active';
                            } else {
                                $menu[$top_key]['sub'][$one_key]['active'] = '';
                            }
                        }
                    }
                }
            }
            $this->app->make('config')->set('admin', $menu);
        }
    }
    public function initAdminConfig() {
        $this->app->make('config')->set('admin', [
            [
                'title' => '概略导航',
                'active' => 'admin',
                'sub' => [
                    [
                        'title' => '仪表盘',
                        'active' => 'admin',
                        'url'   => 'admin',
                        'icon'  => 'fa-dashboard',
                    ]
                ]
            ],
            [
                'title' => '组件导航',
                'active' => '',
                'sub' => [
                    [
                        'title' => '网站管理',
                        'active' => [
                            'admin/site*',
                            'admin/seo*',
                        ],
                        'icon'  => 'fa-cogs',
                        'sub' => [
                            [
                                'title' => '网站信息',
                                'active' => 'admin/site*',
                                'url' => 'admin/site',
                            ],
                            [
                                'title' => 'SEO设置',
                                'active' => 'admin/seo*',
                                'url' => 'admin/seo',
                            ],
                        ]
                    ],
                    [
                        'title' => '菜单管理',
                        'active' => 'admin/menu*',
                        'url'   => 'admin/menu',
                        'icon'  => 'fa-paper-plane',
                    ],
                    [
                        'title' => '内容管理',
                        'active' => [
                            'admin/category*',
                            'admin/article*',
                            'admin/page*',
                            'admin/recycle*',
                        ],
                        'icon'  => 'fa-building',
                        'sub' => [
                            [
                                'title' => '分类管理',
                                'active' => 'admin/category*',
                                'url' => 'admin/category',
                            ],
                            [
                                'title' => '文章管理',
                                'active' => 'admin/article*',
                                'url' => 'admin/article',
                            ],
                            [
                                'title' => '页面管理',
                                'active' => 'admin/page*',
                                'url' => 'admin/page',
                            ],
                            /*[
                                'title' => '回收站',
                                'active' => 'admin/recycle*',
                                'url' => 'admin/recycle',
                            ],*/
                        ]
                    ],
                    [
                        'title' => '组件管理',
                        'active' => [
                            'admin/theme*',
                            'admin/flash*',
                            'admin/ad*',
                        ],
                        'icon'  => 'fa-table',
                        'sub' => [
                            [
                                'title' => '主题',
                                'active' => 'admin/theme*',
                                'url' => 'admin/theme',
                            ],
                            /*[
                                'title' => '幻灯片',
                                'active' => 'admin/flash*',
                                'url' => 'admin/flash',
                            ],
                            [
                                'title' => '广告位',
                                'active' => 'admin/ad*',
                                'url' => 'admin/ad',
                            ],*/
                        ]
                    ],
                ]
            ],
        ]);
    }
    /**
     * @return void
     */
    public function register() {
    }
}