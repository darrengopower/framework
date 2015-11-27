<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 10:10
 */
namespace Notadd\Foundation\Bootstrap;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;
use Notadd\Foundation\AliasLoader;
class RegisterFacades {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        AliasLoader::getInstance($app->make('config')->get('app.aliases'))->register();
    }
}