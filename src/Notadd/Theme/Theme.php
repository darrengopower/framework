<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 23:38
 */
namespace Notadd\Theme;
use Illuminate\Container\Container;
use Notadd\Theme\Contracts\Theme as ThemeContract;
class Theme implements ThemeContract {
    /**
     * @var \Notadd\Foundation\Application
     */
    private $application;
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
    private $cssPath;
    /**
     * @var string
     */
    private $cssStaticPath;
    /**
     * @var string
     */
    private $fontPath;
    /**
     * @var string
     */
    private $fontStaticPath;
    /**
     * @var string
     */
    private $jsPath;
    /**
     * @var string
     */
    private $jsStaticPath;
    /**
     * @var string
     */
    private $imagePath;
    /**
     * @var string
     */
    private $imageStaticPath;
    /**
     * @var string
     */
    private $viewPath;
    /**
     * @var \Notadd\Setting\Factory
     */
    private $setting;
    /**
     * @param $title
     * @param $alias
     */
    public function __construct($title, $alias) {
        $this->alias = $alias;
        $this->application = Container::getInstance();
        $this->setting = $this->application->make('setting');
        $this->title = $title;
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
    public function getCssPath() {
        return $this->cssPath;
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useCssPath($path) {
        $this->cssPath = $path;
    }
    /**
     * @return string
     */
    public function getCssStaticPath() {
        return $this->imageStaticPath ? $this->imageStaticPath : $this->getDefaultStaticPath('css');
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useCssStaticPath($path) {
        $this->cssStaticPath = $path;
    }
    /**
     * @return string
     */
    public function getFontPath() {
        return $this->fontPath;
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useFontPath($path) {
        $this->fontPath = $path;
    }
    /**
     * @return string
     */
    public function getFontStaticPath() {
        return $this->imageStaticPath ? $this->imageStaticPath : $this->getDefaultStaticPath('fonts');
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useFontStaticPath($path) {
        $this->fontStaticPath = $path;
    }
    /**
     * @return string
     */
    public function getJsPath() {
        return $this->jsPath;
    }
    /**
     * @param $path
     * @return mixed|void
     */
    public function useJsPath($path) {
        $this->jsPath = $path;
    }
    /**
     * @return string
     */
    public function getJsStaticPath() {
        return $this->imageStaticPath ? $this->imageStaticPath : $this->getDefaultStaticPath('js');
    }
    /**
     * @param $path
     * @return mixed|void
     */
    public function useJsStaticPath($path) {
        $this->jsStaticPath = $path;
    }
    /**
     * @return string
     */
    public function getImagePath() {
        return $this->imagePath;
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useImagePath($path) {
        $this->imagePath = $path;
    }
    /**
     * @return string
     */
    public function getImageStaticPath() {
        return $this->imageStaticPath ? $this->imageStaticPath : $this->getDefaultStaticPath('images');
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useImageStaticPath($path) {
        $this->imageStaticPath = $path;
    }
    /**
     * @return string
     */
    public function getViewPath() {
        return $this->viewPath;
    }
    /**
     * @param string $path
     * @return mixed|void
     */
    public function useViewPath($path) {
        $this->viewPath = $path;
    }
    /**
     * @return bool
     */
    public function isDefault() {
        if($this->setting->get('site.theme') === $this->alias) {
            return true;
        }
        return false;
    }
    /**
     * @param string $path
     * @return string
     */
    protected function getDefaultStaticPath($path = '') {
        $defaultPath = $this->application->publicPath() . DIRECTORY_SEPARATOR . 'statics' . DIRECTORY_SEPARATOR . $this->alias;
        return $path ? $defaultPath . DIRECTORY_SEPARATOR . $path : $defaultPath;
    }
}