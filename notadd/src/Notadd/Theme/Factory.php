<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 16:32
 */
namespace Notadd\Theme;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\MountManager;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use Notadd\Theme\Events\GetThemeList;
class Factory {
    /**
     * @var
     */
    private $app;
    /**
     * @var
     */
    private $files;
    /**
     * @var
     */
    private $list;
    /**
     * @param Application $app
     * @param Filesystem $files
     */
    public function __construct(Application $app, Filesystem $files) {
        $this->app = $app;
        $this->files = $files;
        $this->buildThemeList();
    }
    /**
     * @return void
     */
    protected function buildThemeList() {
        $list = Collection::make();
        $list->put('default', new Theme('默认模板', 'default', realpath($this->app->basePath() . '/../template/default')));
        $this->app->make('events')->fire(new GetThemeList($this->app, $list));
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
        $list->put('admin', new Theme('后台模板', 'admin', realpath($this->app->basePath() . '/../template/admin')));
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
}