<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 19:58
 */
namespace Notadd\Foundation\Cache\Console;
use Illuminate\Filesystem\Filesystem;
use Notadd\Foundation\Composer\Composer;
use Notadd\Foundation\Console\Command;
class CacheTableCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'cache:table';
    /**
     * @var string
     */
    protected $description = 'Create a migration for the cache database table';
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var \Notadd\Foundation\Composer\Composer
     */
    protected $composer;
    /**
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Notadd\Foundation\Composer\Composer
     */
    public function __construct(Filesystem $files, Composer $composer) {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }
    /**
     * @return void
     */
    public function fire() {
        $fullPath = $this->createBaseMigration();
        $this->files->put($fullPath, $this->files->get(__DIR__ . '/stubs/cache.stub'));
        $this->info('Migration created successfully!');
        $this->composer->dumpAutoloads();
    }
    /**
     * @return string
     */
    protected function createBaseMigration() {
        $name = 'create_cache_table';
        $path = $this->notadd->databasePath() . '/migrations';
        return $this->notadd['migration.creator']->create($name, $path);
    }
}