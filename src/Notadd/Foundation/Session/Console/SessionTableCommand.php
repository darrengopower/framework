<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 19:51
 */
namespace Notadd\Foundation\Session\Console;
use Illuminate\Filesystem\Filesystem;
use Notadd\Foundation\Composer\Composer;
use Notadd\Foundation\Console\Command;
class SessionTableCommand extends Command {
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'session:table';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a migration for the session database table';
    /**
     * The filesystem instance.
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var \Notadd\Foundation\Composer\Composer
     */
    protected $composer;
    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Foundation\Composer\Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer) {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }
    /**
     * Execute the console command.
     * @return void
     */
    public function fire() {
        $fullPath = $this->createBaseMigration();
        $this->files->put($fullPath, $this->files->get(__DIR__ . '/stubs/database.stub'));
        $this->info('Migration created successfully!');
        $this->composer->dumpAutoloads();
    }
    /**
     * @return string
     */
    protected function createBaseMigration() {
        $name = 'create_sessions_table';
        $path = $this->notadd->databasePath() . '/migrations';
        return $this->notadd['migration.creator']->create($name, $path);
    }
}