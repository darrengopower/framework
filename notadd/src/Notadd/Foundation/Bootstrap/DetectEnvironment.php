<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 09:56
 */
namespace Notadd\Foundation\Bootstrap;
use Dotenv;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;
class DetectEnvironment {
    /**
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app) {
        try {
            Dotenv::load($app->environmentPath(), $app->environmentFile());
        } catch(InvalidArgumentException $e) {
        }
        $app->detectEnvironment(function () {
            return 'production';
        });
    }
}