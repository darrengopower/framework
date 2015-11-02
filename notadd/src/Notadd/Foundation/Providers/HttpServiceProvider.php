<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 17:55
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
class HttpServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->get('/', function() {
            $this->app['view']->share('logo', file_get_contents(realpath($this->app->basePath() . '/../template/install') . DIRECTORY_SEPARATOR . 'logo.svg'));
            return $this->app['view']->make('index');
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}