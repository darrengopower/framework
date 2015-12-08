<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:11
 */
namespace Notadd\Setting;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Notadd\Setting\Models\Setting;
class Factory {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;
    /**
     * @var \Illuminate\Cache\CacheManager
     */
    private $cache;
    /**
     * @var string
     */
    private $cache_key = 'notadd_setting';
    public function __construct(Application $application) {
        $this->application = $application;
        $this->cache = $application->make('cache');
    }
    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null) {
        try {
            $settings = $this->cache->rememberForever($this->cache_key, function () {
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
        } catch(Exception $ex) {
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
        if($this->application->isInstalled()) {
            $this->cache->forget($this->cache_key);
        }
        return true;
    }
}