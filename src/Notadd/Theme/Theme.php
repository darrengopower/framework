<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-02 23:38
 */
namespace Notadd\Theme;
use Illuminate\Container\Container;
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
     * @param $title
     * @param $alias
     */
    public function __construct($title, $alias) {
        $this->title = $title;
        $this->alias = $alias;
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
     */
    public function useCssPath($path) {
        $this->cssPath = $path;
    }
    /**
     * @return string
     */
    public function getFontPath() {
        return $this->fontPath;
    }
    /**
     * @param string $path
     */
    public function useFontPath($path) {
        $this->fontPath = $path;
    }
    /**
     * @return string
     */
    public function getJsPath() {
        return $this->jsPath;
    }
    public function useJsPath($path) {
        $this->jsPath = $path;
    }
    /**
     * @return string
     */
    public function getImagePath() {
        return $this->imagePath;
    }
    /**
     * @param string $path
     */
    public function useImagePath($path) {
        $this->imagePath = $path;
    }
    /**
     * @return string
     */
    public function getViewPath() {
        return $this->viewPath;
    }
    /**
     * @param string $path
     */
    public function useViewPath($path) {
        $this->viewPath = $path;
    }
    /**
     * @return bool
     */
    public function isDefault() {
        if(Container::getInstance()->make('setting')->get('site.theme') === $this->alias) {
            return true;
        }
        return false;
    }
}