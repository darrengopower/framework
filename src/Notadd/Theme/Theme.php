<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 23:38
 */
namespace Notadd\Theme;
use Notadd\Setting\Facades\Setting;
class Theme {
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $alias;
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var string
     */
    private $cssPath;
    /**
     * @var string
     */
    private $fontPath;
    /**
     * @var string
     */
    private $jsPath;
    /**
     * @var string
     */
    private $imagePath;
    /**
     * @var string
     */
    private $viewPath;
    /**
     * @param $alias
     * @param $path
     */
    public function __construct($title, $alias, $path) {
        $this->title = $title;
        $this->alias = $alias;
        $this->basePath = $path;
        $this->cssPath = realpath($this->basePath . DIRECTORY_SEPARATOR . 'css');
        $this->fontPath = realpath($this->basePath . DIRECTORY_SEPARATOR . 'fonts');
        $this->jsPath = realpath($this->basePath . DIRECTORY_SEPARATOR . 'js');
        $this->imagePath = realpath($this->basePath . DIRECTORY_SEPARATOR . 'images');
        $this->viewPath = realpath($this->basePath . DIRECTORY_SEPARATOR . 'views');
    }
    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }
    /**
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }
    /**
     * @return string
     */
    public function getCssPath() {
        return $this->cssPath;
    }
    /**
     * @return string
     */
    public function getFontPath() {
        return $this->fontPath;
    }
    /**
     * @return string
     */
    public function getJsPath() {
        return $this->jsPath;
    }
    /**
     * @return string
     */
    public function getImagePath() {
        return $this->imagePath;
    }
    /**
     * @return string
     */
    public function getViewPath() {
        return $this->viewPath;
    }
    /**
     * @return bool
     */
    public function isDefault() {
        if(Setting::get('site.theme') === $this->alias) {
            return true;
        }
        return false;
    }
}