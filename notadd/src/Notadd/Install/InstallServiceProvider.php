<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Install;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
class InstallServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->app['router']->get('/', function(ServerRequestInterface $request) {
            dd($request);
        });
    }
    /**
     * @return void
     */
    public function register() {
    }
}