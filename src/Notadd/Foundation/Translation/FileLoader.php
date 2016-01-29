<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-05 00:09
 */
namespace Notadd\Foundation\Translation;
use Illuminate\Filesystem\Filesystem;
/**
 * Class FileLoader
 * @package Notadd\Foundation\Translation
 */
class FileLoader implements LoaderInterface {
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var array
     */
    protected $hints = [];
    /**
     * FileLoader constructor.
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string $path
     */
    public function __construct(Filesystem $files, $path) {
        $this->path = $path;
        $this->files = $files;
    }
    /**
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null) {
        if(is_null($namespace) || $namespace == '*') {
            return $this->loadPath($this->path, $locale, $group);
        }
        return $this->loadNamespaced($locale, $group, $namespace);
    }
    /**
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    protected function loadNamespaced($locale, $group, $namespace) {
        if(isset($this->hints[$namespace])) {
            $lines = $this->loadPath($this->hints[$namespace], $locale, $group);
            return $this->loadNamespaceOverrides($lines, $locale, $group, $namespace);
        }
        return [];
    }
    /**
     * @param array $lines
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace) {
        $file = "{$this->path}/vendor/{$namespace}/{$locale}/{$group}.php";
        if($this->files->exists($file)) {
            return array_replace_recursive($lines, $this->files->getRequire($file));
        }
        return $lines;
    }
    /**
     * @param string $path
     * @param string $locale
     * @param string $group
     * @return array
     */
    protected function loadPath($path, $locale, $group) {
        if($this->files->exists($full = "{$path}/{$locale}/{$group}.php")) {
            return $this->files->getRequire($full);
        }
        return [];
    }
    /**
     * @param string $namespace
     * @param string $hint
     */
    public function addNamespace($namespace, $hint) {
        $this->hints[$namespace] = $hint;
    }
}