<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Install;
use Illuminate\Support\ServiceProvider;
use Notadd\Install\Contracts\Prerequisite;
use Notadd\Install\Prerequisites\Composite;
use Notadd\Install\Prerequisites\PhpExtensions;
use Notadd\Install\Prerequisites\PhpVersion;
use Notadd\Install\Prerequisites\WritablePaths;
class InstallServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom(__DIR__ . '/../../../views/install', 'install');
        $this->app->make('router')->get('/', 'Notadd\Install\Controllers\PrerequisiteController@render');
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->bind(
            Prerequisite::class,
            function() {
                return new Composite(
                    new PhpVersion(),
                    new PhpExtensions(),
                    new WritablePaths()
                );
            }
        );
    }
}