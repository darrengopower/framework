<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-20 15:42
 */
namespace Notadd\Theme;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Notadd\Theme\Contracts\Material as MaterialContract;
class Material implements MaterialContract {
    /**
     * @var \Notadd\Foundation\Application
     */
    protected $application;
    /**
     * @var \Notadd\Theme\Compiler
     */
    protected $compiler;
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
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
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
     * @var \Notadd\Theme\Factory
     */
    protected $theme;
    /**
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;
    /**
     * @param Application $application
     * @param \Notadd\Theme\Compiler $compiler
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Theme\FileFinder $finder
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Routing\UrlGenerator $url
     */
    public function __construct(Application $application, Compiler $compiler, Filesystem $files, FileFinder $finder, Request $request, UrlGenerator $url) {
        $this->application = $application;
        $this->compiler = $compiler;
        $this->defaultCssMaterial = new Collection();
        $this->defaultJsMaterial = new Collection();
        $this->defaultLessMaterial = new Collection();
        $this->defaultSassMaterial = new Collection();
        $this->extendCssMaterial = new Collection();
        $this->extendJsMaterial = new Collection();
        $this->extendLessMaterial = new Collection();
        $this->extendSassMaterial = new Collection();
        $this->files = $files;
        $this->finder = $finder;
        $this->layoutCssMaterial = new Collection();
        $this->layoutJsMaterial = new Collection();
        $this->layoutLessMaterial = new Collection();
        $this->layoutSassMaterial = new Collection();
        $this->request = $request;
        $this->url = $url;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    protected function compileStyleMaterial() {
        $layout = $this->layoutLessMaterial->merge($this->layoutSassMaterial)->merge($this->layoutCssMaterial);
        $default = $this->defaultLessMaterial->merge($this->defaultSassMaterial)->merge($this->defaultCssMaterial);
        $extend = $this->extendLessMaterial->merge($this->extendSassMaterial)->merge($this->extendCssMaterial);
        $files = new Collection();
        $materials = new Collection();
        $layout->merge($default)->merge($extend)->each(function($value) use($files) {
            $files->push($this->findPath($value));
        });
        $path = md5($this->request->path());
        $dictionary = new Collection();
        $dictionary->push($this->application->publicPath());
        $dictionary->push('cache');
        $dictionary = $this->pathSplit($path, '2,2,2,2,2,2', $dictionary);
        $dictionary = $dictionary->implode(DIRECTORY_SEPARATOR);
        if(!$this->files->isDirectory($dictionary)) {
            $this->files->makeDirectory($dictionary, '0755', true);
        }
        $file = $dictionary . DIRECTORY_SEPARATOR . Str::substr($path, 12, 20) . '.css';
        file_put_contents($file, $this->compileStyle($files));
        return $this->pathSplit($path, '2,2,2,2,2,2,20', Collection::make([
            'cache'
        ]))->implode('/') . '.css';
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    protected function collectingScriptMaterial() {
        $jsMaterials = $this->layoutJsMaterial->merge($this->defaultJsMaterial)->merge($this->extendJsMaterial);
    }
    /**
     * @param \Illuminate\Support\Collection $files
     * @return string
     */
    protected function compileStyle($files) {
        return $this->compiler->compileLess($files);
    }
    /**
     * @param $path
     * @return string
     */
    public function findHint($path) {
        return Str::substr($path, 0, strpos($path, '::'));
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
     * @param $path
     * @return bool|string
     */
    protected function findPath($path) {
        $hint = $this->findHint($path);
        $theme = $this->theme->getTheme($hint);
        $type = $this->findType($path);
        $folder = '';
        switch($type) {
            case 'css':
                $folder = $theme->getCssPath();
                break;
            case 'js':
                $folder = $theme->getJsPath();
                break;
            case 'less':
                $folder = $theme->getLessPath();
                break;
            case 'sass':
                $folder = $theme->getSassPath();
                break;
        }
        $location = $this->findLocation($path);
        $file = $folder . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, str_replace($hint . '::' . $type . '.' . $location . '.', '', $path)) . '.' . $type;
        if(file_exists($file)) {
            return $file;
        } else {
            return false;
        }
    }
    /**
     * @param $path
     * @return string
     */
    protected function findType($path) {
        return str_replace(Str::substr($path, strpos($path, '.')), '', Str::substr($path, strpos($path, '::') + 2));
    }
    /**
     * @return string
     */
    public function outputCssInBlade() {
        $path = $this->compileStyleMaterial();
        return '<link href="' . $this->url->asset($path) . '" rel="stylesheet">';
    }
    /**
     * @return string
     */
    public function outputJsInBlade() {
        return '';
    }
    /**
     * @param string $path
     * @param string $dots
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    protected function pathSplit($path, $dots, $data = null) {
        $dots = explode(',', $dots);
        $data = $data ? $data : new Collection();
        $offset = 0;
        foreach($dots as $dot) {
            $data->push(Str::substr($path, $offset, $dot));
            $offset += $dot;
        }
        return $data;
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
    /**
     * @param \Notadd\Theme\Factory $theme
     */
    public function setTheme($theme) {
        $this->theme = $theme;
    }
}