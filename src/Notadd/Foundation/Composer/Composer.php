<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 17:09
 */
namespace Notadd\Foundation\Composer;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
class Composer {
    /**
     * The filesystem instance.
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * The working path to regenerate from.
     * @var string
     */
    protected $workingPath;
    /**
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  string $workingPath
     */
    public function __construct(Filesystem $files, $workingPath = null) {
        $this->files = $files;
        $this->workingPath = $workingPath;
    }
    /**
     * @param  string $extra
     * @return void
     */
    public function dumpAutoloads($extra = '') {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer() . ' dump-autoload ' . $extra));
        $process->run();
    }
    /**
     * @return void
     */
    public function dumpOptimized() {
        $this->dumpAutoloads('--optimize');
    }
    /**
     * @return string
     */
    protected function findComposer() {
        if($this->files->exists($this->workingPath . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }
        return 'composer';
    }
    /**
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess() {
        return (new Process('', $this->workingPath))->setTimeout(null);
    }
    /**
     * @param  string $path
     * @return $this
     */
    public function setWorkingPath($path) {
        $this->workingPath = realpath($path);
        return $this;
    }
}