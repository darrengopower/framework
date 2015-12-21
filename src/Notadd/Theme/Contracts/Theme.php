<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-18 14:55
 */
namespace Notadd\Theme\Contracts;
interface Theme {
    /**
     * @return mixed
     */
    public function getTitle();
    /**
     * @return mixed
     */
    public function getAlias();
    /**
     * @return mixed
     */
    public function getCssPath();
    /**
     * @param $path
     * @return mixed
     */
    public function useCssPath($path);
    /**
     * @return mixed
     */
    public function getFontPath();
    /**
     * @param $path
     * @return mixed
     */
    public function useFontPath($path);
    /**
     * @return mixed
     */
    public function getJsPath();
    /**
     * @param $path
     * @return mixed
     */
    public function useJsPath($path);
    /**
     * @return mixed
     */
    public function getImagePath();
    /**
     * @param $path
     * @return mixed
     */
    public function useImagePath($path);
    /**
     * @return mixed
     */
    public function getViewPath();
    /**
     * @param $path
     * @return mixed
     */
    public function useViewPath($path);
    /**
     * @return mixed
     */
    public function isDefault();
}