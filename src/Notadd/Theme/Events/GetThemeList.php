<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 18:21
 */
namespace Notadd\Theme\Events;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Notadd\Theme\Theme;
class GetThemeList {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $list;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Support\Collection $list
     */
    public function __construct(Application $app, Collection $list) {
        $this->app = $app;
        $this->list = $list;
    }
    /**
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function register($key, $value) {
        if($value instanceof Theme) {
            $this->list->put($key, $value);
        } else {
            throw new Exception('正在注册未知类型的主题！');
        }
    }
}