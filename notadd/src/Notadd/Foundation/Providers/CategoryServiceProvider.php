<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:46
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Category\Listeners\BeforeCategoryDelete;
use Notadd\Category\Models\Category;
class CategoryServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->before(function() {
            $categories = Category::whereEnabled(true)->get();
            foreach($categories as $category) {
                if($category->alias) {
                    $this->app['router']->get($category->alias . '/{id}', 'Notadd\Article\Controllers\ArticleController@show')->where('id', '[0-9]+');
                    $this->app['router']->get($category->alias, function() use ($category) {
                        return $this->app->call('Notadd\Category\Controllers\CategoryController@show', ['id' => $category->id]);
                    });
                }
            }
        });
        $this->app['router']->group(['namespace' => 'Notadd\Category\Controllers'], function () {
            $this->app['router']->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->app['router']->resource('category', 'CategoryController');
                $this->app['router']->post('category/{id}/status', 'CategoryController@status');
            });
            $this->app['router']->resource('category', 'CategoryController');
        });
        $this->app->make('events')->subscribe(BeforeCategoryDelete::class);
    }
    /**
     * @return void
     */
    public function register() {
    }
}