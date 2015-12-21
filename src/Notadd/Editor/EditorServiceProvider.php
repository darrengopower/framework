<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-10 17:21
 */
namespace Notadd\Editor;
use Illuminate\Support\ServiceProvider;
class EditorServiceProvider extends ServiceProvider {
    /**
     * @return void
     */
    public function boot() {
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->singleton('editor', Editor::class);
        $this->app->singleton('editor.ueditor', '');
    }
}