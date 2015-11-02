<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 18:21
 */
namespace Notadd\Theme\Events;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
class GetThemeList {
    private $app;
    private $list;
    public function __construct(Application $app, Collection $list) {
        $this->app = $app;
        $this->list = $list;
    }
    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        $this->list->put($key, $value);
    }
}