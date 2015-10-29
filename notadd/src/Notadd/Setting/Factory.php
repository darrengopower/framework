<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:11
 */
namespace Notadd\Setting;
use Illuminate\Support\Facades\Cache;
use Notadd\Setting\Models\Setting;
class Factory {
    /**
     * @var string
     */
    private $cache_key = 'setting';
    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null) {
        try {
            $settings = Cache::rememberForever($this->cache_key, function () {
                $settings = Setting::get([
                    'key',
                    'value'
                ]);
                $arr = [];
                foreach($settings as $setting) {
                    $arr[$setting->key] = $setting->value;
                }
                return $arr;
            });
            return (isset($settings[$key])) ? $settings[$key] : $default;
        } catch(\Exception $ex) {
            return null;
        }
    }
    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value) {
        $setting = Setting::whereKey($key)->first();
        if(!is_object($setting)) {
            $setting = new Setting();
            $setting->key = $key;
        }
        $setting->value = $value;
        $setting->save();
        Cache::forget($this->cache_key);
        return true;
    }
}