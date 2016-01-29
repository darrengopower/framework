<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 17:43
 */
namespace Notadd\Foundation\Database\Console\Seeds;
use Illuminate\Filesystem\Filesystem;
use Notadd\Foundation\Composer\Composer;
use Notadd\Foundation\Console\GeneratorCommand;
/**
 * Class SeederMakeCommand
 * @package Notadd\Foundation\Database\Console\Seeds
 */
class SeederMakeCommand extends GeneratorCommand {
    /**
     * @var string
     */
    protected $name = 'make:seeder';
    /**
     * @var string
     */
    protected $description = 'Create a new seeder class';
    /**
     * @var string
     */
    protected $type = 'Seeder';
    /**
     * @var \Notadd\Foundation\Composer\Composer
     */
    protected $composer;
    /**
     * SeederMakeCommand constructor.
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Foundation\Composer\Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer) {
        parent::__construct($files);
        $this->composer = $composer;
    }
    /**
     * @return void
     */
    public function fire() {
        parent::fire();
        $this->composer->dumpAutoloads();
    }
    /**
     * @return string
     */
    protected function getStub() {
        return __DIR__ . '/stubs/seeder.stub';
    }
    /**
     * @param string $name
     * @return string
     */
    protected function getPath($name) {
        return $this->notadd->frameworkPath() . '/seeds/' . $name . '.php';
    }
    /**
     * @param string $name
     * @return string
     */
    protected function parseName($name) {
        return $name;
    }
}