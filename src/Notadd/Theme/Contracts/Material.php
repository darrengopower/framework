<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-20 15:39
 */
namespace Notadd\Theme\Contracts;
use Illuminate\Http\Request;
use Notadd\Theme\FileFinder as ThemeFileFinder;
interface Material {
    /**
     * @param \Notadd\Theme\FileFinder $finder
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(ThemeFileFinder $finder, Request $request);
    /**
     * @return string
     */
    public function outputCssInBlade();
    /**
     * @return string
     */
    public function outputJsInBlade();
    /**
     * @param string $path
     * @return void
     */
    public function registerCssMaterial($path);
    /**
     * @param string $path
     * @return void
     */
    public function registerJsMaterial($path);
    /**
     * @param string $path
     * @return void
     */
    public function registerLessMaterial($path);
    /**
     * @param string $path
     * @return void
     */
    public function registerSassMaterial($path);
}