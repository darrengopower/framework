<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-21 14:57
 */
namespace Notadd\Foundation\SearchEngine;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\View\Factory;
use Notadd\Setting\Factory as SettingFactory;
/**
 * Class Optimization
 * @package Notadd\Foundation\SearchEngine
 */
class Optimization {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $code;
    /**
     * @var \Notadd\Foundation\SearchEngine\Meta
     */
    private $meta;
    /**
     * @var \Notadd\Setting\Factory
     */
    private $setting;
    /**
     * @var \Illuminate\View\Factory
     */
    private $view;
    /**
     * Optimization constructor.
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Notadd\Setting\Factory $setting
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(Application $app, SettingFactory $setting, Factory $view) {
        $this->app = $app;
        $this->view = $view;
        $this->code = new Collection();
        $this->meta = new Meta();
        $this->setting = $setting;
        $this->code->put('{sitename}', $this->setting->get('seo.title', 'Notadd CMS'));
    }
    /**
     * @param string $key
     * @return static
     */
    public function getData($key = '') {
        $data = $this->meta->getData();
        foreach($data as $k=>$v) {
            $data->put($k, strip_tags(trim(strtr($v, $this->code->toArray()), '-_ ')));
        }
        if($key) {
            return $data->get($key);
        } else {
            return $data;
        }
    }
    /**
     * @param $key
     * @param $value
     */
    public function setCode($key, $value) {
        $this->code->put($key, $value);
    }
    /**
     * @param $title
     * @param $description
     * @param $keywords
     */
    public function setCustomMeta($title, $description, $keywords) {
        if($title || $keywords || $description) {
            $this->meta->setTitle($title);
            $this->meta->setDescription($description);
            $this->meta->setKeywords($keywords);
        }
    }
    /**
     * @param $title
     */
    public function setTitleMeta($title) {
        $this->meta->setTitle($title);
    }
    /**
     * @param $description
     */
    public function setDescriptionMeta($description) {
        $this->meta->setDescription($description);
    }
    /**
     * @param $keywords
     */
    public function setKeywordsMeta($keywords) {
        $this->meta->setKeywords($keywords);
    }
}