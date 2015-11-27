<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Setting\Facades;
use Illuminate\Support\Facades\Facade;
class Setting extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'setting';
    }
}