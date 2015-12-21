<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-18 17:13
 */
namespace Notadd\Theme;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Factory as ViewFactory;
use Notadd\Theme\Contracts\FileFinder as FileFinderContract;
class FileFinder implements FileFinderContract {
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;
    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(Filesystem $files, ViewFactory $view) {
        $this->files = $files;
        $this->view = $view;
    }
    /**
     * @param $path
     * @return bool
     */
    public function exits($path) {
        return true;
    }
    /**
     * @param $path
     * @return mixed|void
     */
    public function find($path) {
        $hints = $this->getViewHints();
    }
    /**
     * @return array
     */
    public function getViewHints() {
        return $this->view->getFinder()->getHints();
    }
}