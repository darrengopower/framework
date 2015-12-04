<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-05 00:23
 */
namespace Notadd\Foundation\Translation;
use Illuminate\Support\ServiceProvider;
class TranslationServiceProvider extends ServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @return void
     */
    public function register() {
        $this->registerLoader();
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            $trans = new Translator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            return $trans;
        });
    }
    /**
     * @return void
     */
    protected function registerLoader() {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });
    }
    /**
     * @return array
     */
    public function provides() {
        return [
            'translator',
            'translation.loader'
        ];
    }
}