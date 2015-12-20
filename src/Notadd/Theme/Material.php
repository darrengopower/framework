<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-20 15:42
 */
namespace Notadd\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Notadd\Theme\Contracts\Material as MaterialContract;
class Material implements MaterialContract {
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $defaultCssMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $defaultJsMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $defaultLessMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $defaultSassMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $extendCssMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $extendJsMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $extendLessMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $extendSassMaterial;
    /**
     * @var \Notadd\Theme\FileFinder
     */
    protected $finder;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $layoutCssMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $layoutJsMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $layoutLessMaterial;
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $layoutSassMaterial;
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    /**
     * @param \Notadd\Theme\FileFinder $finder
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(FileFinder $finder, Request $request) {
        $this->defaultCssMaterial = new Collection();
        $this->defaultJsMaterial = new Collection();
        $this->defaultLessMaterial = new Collection();
        $this->defaultSassMaterial = new Collection();
        $this->extendCssMaterial = new Collection();
        $this->extendJsMaterial = new Collection();
        $this->extendLessMaterial = new Collection();
        $this->extendSassMaterial = new Collection();
        $this->finder = $finder;
        $this->layoutCssMaterial = new Collection();
        $this->layoutJsMaterial = new Collection();
        $this->layoutLessMaterial = new Collection();
        $this->layoutSassMaterial = new Collection();
        $this->request = $request;
    }
    protected function collectingStyleMaterial() {
        $cssMaterials = $this->layoutCssMaterial->merge($this->defaultCssMaterial)->merge($this->extendCssMaterial);
        $lessMaterials = $this->layoutLessMaterial->merge($this->defaultLessMaterial)->merge($this->extendLessMaterial);
        $sassMaterials = $this->layoutSassMaterial->merge($this->defaultSassMaterial)->merge($this->extendSassMaterial);
        dd($lessMaterials);
    }
    /**
     * @param $path
     * @return string
     */
    public function findHint($path) {
        $length = strpos($path, '::');
        return Str::substr($path, 0, $length);
    }
    /**
     * @param $path
     * @return string
     */
    protected function findLocation($path) {
        $first = strpos($path, '.') + 1;
        $second = strpos($path, '.', $first);
        return str_replace(Str::substr($path, $second), '', Str::substr($path, $first));
    }
    /**
     * @return string
     */
    public function outputCssInBlade() {
        $files = $this->collectingStyleMaterial();
        return '';
    }
    /**
     * @return string
     */
    public function outputJsInBlade() {
        return '';
    }
    /**
     * @param string $path
     * @return void
     */
    public function registerCssMaterial($path) {
        switch($this->findLocation($path)) {
            case 'default':
                $this->defaultCssMaterial->push($path);
                break;
            case 'extend':
                $this->extendCssMaterial->push($path);
                break;
            case 'layout':
                $this->layoutCssMaterial->push($path);
                break;
        }
    }
    /**
     * @param string $path
     * @return void
     */
    public function registerJsMaterial($path) {
    }
    /**
     * @param string $path
     * @return void
     */
    public function registerLessMaterial($path) {
        switch($this->findLocation($path)) {
            case 'default':
                $this->defaultLessMaterial->push($path);
                break;
            case 'extend':
                $this->extendLessMaterial->push($path);
                break;
            case 'layout':
                $this->layoutLessMaterial->push($path);
                break;
        }
    }
    /**
     * @param string $path
     * @return void
     */
    public function registerSassMaterial($path) {
        switch($this->findLocation($path)) {
            case 'default':
                $this->defaultSassMaterial->push($path);
                break;
            case 'extend':
                $this->extendSassMaterial->push($path);
                break;
            case 'layout':
                $this->layoutSassMaterial->push($path);
                break;
        }
    }
}