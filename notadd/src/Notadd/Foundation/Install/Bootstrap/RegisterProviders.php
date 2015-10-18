<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Install\Bootstrap;
use Notadd\Foundation\Application;
class RegisterProviders {
    private $app;
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        $this->app = $app;
        foreach($this->app->config['app.providers'] as $provider) {
            $this->app->register($this->createProvider($provider));
        }
    }
    /**
     * @param  string $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function createProvider($provider) {
        return new $provider($this->app);
    }
}