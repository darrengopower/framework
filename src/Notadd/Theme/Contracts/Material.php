<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-20 15:39
 */
namespace Notadd\Theme\Contracts;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Notadd\Theme\Compiler;
use Notadd\Theme\FileFinder as ThemeFileFinder;
/**
 * Interface Material
 * @package Notadd\Theme\Contracts
 */
interface Material {
    /**
     * Material constructor.
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Cache\CacheManager $cache
     * @param \Notadd\Theme\Compiler $compiler
     * @param \Illuminate\Events\Dispatcher $dispatcher
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Theme\FileFinder $finder
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Routing\UrlGenerator $url
     */
    public function __construct(Application $application, CacheManager $cache, Compiler $compiler, Dispatcher $dispatcher, Filesystem $files, ThemeFileFinder $finder, Request $request, UrlGenerator $url);
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