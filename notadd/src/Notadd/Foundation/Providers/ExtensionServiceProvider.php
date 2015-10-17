<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 20:39
 */
namespace Notadd\Foundation\Providers;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Extension\ExtensionManager;
class ExtensionServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function register() {
        $this->app->bind('extension', ExtensionManager::class);
    }
}