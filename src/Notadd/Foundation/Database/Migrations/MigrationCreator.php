<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:06
 */
namespace Notadd\Foundation\Database\Migrations;
use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
class MigrationCreator {
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var array
     */
    protected $postCreate = [];
    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files) {
        $this->files = $files;
    }
    /**
     * @param string $name
     * @param string $path
     * @param string $table
     * @param bool $create
     * @return string
     */
    public function create($name, $path, $table = null, $create = false) {
        $path = $this->getPath($name, $path);
        $stub = $this->getStub($table, $create);
        $this->files->put($path, $this->populateStub($name, $stub, $table));
        $this->firePostCreateHooks();
        return $path;
    }
    /**
     * @param string $table
     * @param bool $create
     * @return string
     */
    protected function getStub($table, $create) {
        if(is_null($table)) {
            return $this->files->get($this->getStubPath() . '/blank.stub');
        }
        else {
            $stub = $create ? 'create.stub' : 'update.stub';
            return $this->files->get($this->getStubPath() . "/{$stub}");
        }
    }
    /**
     * @param string $name
     * @param string $stub
     * @param string $table
     * @return string
     */
    protected function populateStub($name, $stub, $table) {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);
        if(!is_null($table)) {
            $stub = str_replace('DummyTable', $table, $stub);
        }
        return $stub;
    }
    /**
     * @param string $name
     * @return string
     */
    protected function getClassName($name) {
        return Str::studly($name);
    }
    /**
     * @return void
     */
    protected function firePostCreateHooks() {
        foreach($this->postCreate as $callback) {
            call_user_func($callback);
        }
    }
    /**
     * @param \Closure $callback
     * @return void
     */
    public function afterCreate(Closure $callback) {
        $this->postCreate[] = $callback;
    }
    /**
     * @param string $name
     * @param string $path
     * @return string
     */
    protected function getPath($name, $path) {
        return $path . '/' . $this->getDatePrefix() . '_' . $name . '.php';
    }
    /**
     * @return string
     */
    protected function getDatePrefix() {
        return date('Y_m_d_His');
    }
    /**
     * @return string
     */
    public function getStubPath() {
        return __DIR__ . '/stubs';
    }
    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem() {
        return $this->files;
    }
}