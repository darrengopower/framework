<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 10:16
 */
namespace Notadd\Foundation\Bootstrap;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
class SetRequestForConsole {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $url = $app->make('config')->get('app.url', 'http://localhost');
        $app->instance('request', Request::create($url, 'GET', [], [], [], $_SERVER));
    }
}