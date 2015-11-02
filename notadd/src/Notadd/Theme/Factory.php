<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:32
 */
namespace Notadd\Theme;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Notadd\Theme\Events\GetThemeList;
class Factory {
    private $app;
    private $list;
    public function __construct(Application $app) {
        $this->app = $app;
        $this->buildThemeList();
    }
    protected function buildThemeList() {
        $list = Collection::make();
        $list->put('default', '默认模板');
        $this->app['events']->fire(new GetThemeList($this->app, $list));
        $this->list = $list;
    }
    public function getThemeList() {
        return $this->list;
    }
}