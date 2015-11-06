<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 17:55
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Notadd\Page\Models\Page;
class HttpServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app->make('router')->get('/', function() {
            $home = $this->app->make('setting')->get('site.home');
            if($home == 'default') {
                $this->app->make('view')->share('logo', file_get_contents(realpath($this->app->basePath() . '/../template/install') . DIRECTORY_SEPARATOR . 'logo.svg'));
                return $this->app->make('view')->make('index');
            } else {
                if(Str::contains($home, 'page_')) {
                    $id = Str::substr($home, 5);
                    if(Page::whereEnabled(true)->whereId($id)->count()) {
                        return $this->app->call('Notadd\Page\Controllers\PageController@show', ['id' => $id]);
                    }
                }
            }
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}