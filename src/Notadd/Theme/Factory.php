<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:32
 */
namespace Notadd\Theme;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\MountManager;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use Notadd\Theme\Contracts\Factory as FactoryContract;
use Notadd\Theme\Events\GetThemeList;
class Factory implements FactoryContract {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;
    /**
     * @var \Notadd\Theme\FileFinder
     */
    private $finder;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $list;
    /**
     * @var \Notadd\Theme\Material
     */
    private $material;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Theme\FileFinder $finder
     * @param \Notadd\Theme\Material $material
     */
    public function __construct(Application $application, Filesystem $files, FileFinder $finder, Material $material) {
        $this->application = $application;
        $this->files = $files;
        $this->finder = $finder;
        $this->material = $material;
        $this->buildThemeList();
    }
    /**
     * @return void
     */
    protected function buildThemeList() {
        $list = Collection::make();
        $default = new Theme('默认模板', 'default');
        $default->useCssPath(realpath($this->application->frameworkPath() . '/less/default'));
        $default->useFontPath(realpath($this->application->frameworkPath() . '/fonts'));
        $default->useImagePath(realpath($this->application->frameworkPath() . '/images/default'));
        $default->useJsPath(realpath($this->application->frameworkPath() . '/js/default'));
        $default->useViewPath(realpath($this->application->frameworkPath() . '/views/default'));
        $list->put('default', $default);
        $admin = new Theme('后台模板', 'admin');
        $admin->useCssPath(realpath($this->application->frameworkPath() . '/less/admin'));
        $admin->useFontPath(realpath($this->application->frameworkPath() . '/fonts'));
        $admin->useImagePath(realpath($this->application->frameworkPath() . '/images/admin'));
        $admin->useJsPath(realpath($this->application->frameworkPath() . '/js/admin'));
        $admin->useViewPath(realpath($this->application->frameworkPath() . '/views/admin'));
        $list->put('admin', $admin);
        $this->application->make('events')->fire(new GetThemeList($this->application, $list));
        $this->list = $list;
    }
    /**
     * @return mixed
     */
    public function getThemeList() {
        return $this->list;
    }
    /**
     * @return void
     */
    public function publishAssets() {
        $list = $this->list;
        $list->put('admin', new Theme('后台模板', 'admin', realpath($this->application->basePath() . '/../template/admin')));
        foreach($list as $theme) {
            $this->publishTag($theme->getAlias());
        }
    }
    /**
     * @param $tag
     */
    private function publishTag($tag) {
        $paths = ServiceProvider::pathsToPublish(null, $tag);
        if(empty($paths)) {
            return;
        }
        foreach($paths as $from => $to) {
            if($this->files->isFile($from)) {
                $this->publishFile($from, $to);
            } elseif($this->files->isDirectory($from)) {
                $this->publishDirectory($from, $to);
            } else {
                continue;
            }
        }
    }
    /**
     * @param $from
     * @param $to
     */
    protected function publishFile($from, $to) {
        $this->createParentDirectory(dirname($to));
        $this->files->copy($from, $to);
    }
    /**
     * @param $from
     * @param $to
     */
    protected function publishDirectory($from, $to) {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to)),
        ]);
        foreach($manager->listContents('from://', true) as $file) {
            $manager->put('to://' . $file['path'], $manager->read('from://' . $file['path']));
        }
    }
    /**
     * @param $directory
     */
    protected function createParentDirectory($directory) {
        if(!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
    /**
     * @param string $path
     * @return mixed|void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function registerCss($path) {
        if($this->finder->exits($path)) {
            switch(str_replace(Str::substr($path, strpos($path, '.')), '', Str::substr($path, strpos($path, '::') + 2))) {
                case 'css':
                    $this->material->registerCssMaterial($path);
                    break;
                case 'less':
                    $this->material->registerLessMaterial($path);
                    break;
                case 'sass':
                    $this->material->registerSassMaterial($path);
                    break;
            }
        } else {
            throw new FileNotFoundException('Css file [' . $path . '] does not exits!');
        }
    }
    /**
     * @param string $path
     * @return mixed|void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function registerJs($path) {
        if($this->finder->exits($path)) {
            $this->material->registerJsMaterial($path);
        } else {
            throw new FileNotFoundException('Js file [' . $path . '] does not exits!');
        }
    }
    /**
     * @param string $type
     * @return string
     */
    public function outputInBlade($type = 'css') {
        $output = '';
        switch($type) {
            case 'css':
                $output = $this->material->outputCssInBlade();
                break;
            case 'js':
                $output = $this->material->outputJsInBlade();
                break;
        }
        return $output;
    }
}