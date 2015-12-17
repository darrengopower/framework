<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-07 16:49
 */
namespace Notadd\Foundation\Validation;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
class ValidationServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function register() {
        $this->registerValidationResolverHook();
        $this->registerPresenceVerifier();
        $this->registerValidationFactory();
    }
    /**
     * @return void
     */
    protected function registerValidationResolverHook() {
        $this->app->afterResolving(function (ValidatesWhenResolved $resolved) {
            $resolved->validate();
        });
    }
    /**
     * @return void
     */
    protected function registerValidationFactory() {
        $this->app->singleton('validator', function ($app) {
            $validator = new Factory($app['translator'], $app);
            if(isset($app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }
            return $validator;
        });
    }
    /**
     * @return void
     */
    protected function registerPresenceVerifier() {
        $this->app->singleton('validation.presence', function ($app) {
            return new DatabasePresenceVerifier($app['db']);
        });
    }
}