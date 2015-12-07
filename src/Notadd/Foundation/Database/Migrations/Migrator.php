<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 01:40
 */
namespace Notadd\Foundation\Database\Migrations;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Notadd\Foundation\Database\ConnectionResolverInterface as Resolver;
class Migrator {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $application;
    /**
     * @var \Notadd\Foundation\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var \Notadd\Foundation\Database\ConnectionResolverInterface
     */
    protected $resolver;
    /**
     * @var string
     */
    protected $connection;
    /**
     * @var array
     */
    protected $notes = [];
    /**
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Notadd\Foundation\Database\Migrations\MigrationRepositoryInterface $repository
     * @param \Notadd\Foundation\Database\ConnectionResolverInterface $resolver
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Application $application, MigrationRepositoryInterface $repository, Resolver $resolver, Filesystem $files) {
        $this->application = $application;
        $this->files = $files;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }
    /**
     * @param string $path
     * @param bool $pretend
     * @return void
     */
    public function run($path, $pretend = false) {
        $this->notes = [];
        $files = $this->getMigrationFiles($path);
        $ran = $this->repository->getRan();
        $migrations = array_diff($files, $ran);
        $this->requireFiles($path, $migrations);
        $this->runMigrationList($migrations, $pretend);
    }
    /**
     * @param array $migrations
     * @param bool $pretend
     * @return void
     */
    public function runMigrationList($migrations, $pretend = false) {
        if(count($migrations) == 0) {
            $this->note('<info>Nothing to migrate.</info>');
            return;
        }
        $batch = $this->repository->getNextBatchNumber();
        foreach($migrations as $file) {
            $this->runUp($file, $batch, $pretend);
        }
    }
    /**
     * @param string $file
     * @param int $batch
     * @param bool $pretend
     * @return void
     */
    protected function runUp($file, $batch, $pretend) {
        $migration = $this->resolve($file);
        if($pretend) {
            return $this->pretendToRun($migration, 'up');
        }
        $migration->up();
        $this->repository->log($file, $batch);
        $this->note("<info>Migrated:</info> $file");
    }
    /**
     * @param bool $pretend
     * @return int
     */
    public function rollback($pretend = false) {
        $this->notes = [];
        $migrations = $this->repository->getLast();
        $count = count($migrations);
        if($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            foreach($migrations as $migration) {
                $this->runDown((object)$migration, $pretend);
            }
        }
        return $count;
    }
    /**
     * @param bool $pretend
     * @return int
     */
    public function reset($pretend = false) {
        $this->notes = [];
        $migrations = array_reverse($this->repository->getRan());
        $count = count($migrations);
        if($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            foreach($migrations as $migration) {
                $this->runDown((object)['migration' => $migration], $pretend);
            }
        }
        return $count;
    }
    /**
     * @param object $migration
     * @param bool $pretend
     * @return void
     */
    protected function runDown($migration, $pretend) {
        $file = $migration->migration;
        $instance = $this->resolve($file);
        if($pretend) {
            return $this->pretendToRun($instance, 'down');
        }
        $instance->down();
        $this->repository->delete($migration);
        $this->note("<info>Rolled back:</info> $file");
    }
    /**
     * @param string $path
     * @return array
     */
    public function getMigrationFiles($path) {
        $files = $this->files->glob($path . '/*_*.php');
        if($files === false) {
            return [];
        }
        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);
        sort($files);
        return $files;
    }
    /**
     * @param string $path
     * @param array $files
     * @return void
     */
    public function requireFiles($path, array $files) {
        foreach($files as $file) {
            $this->files->requireOnce($path . '/' . $file . '.php');
        }
    }
    /**
     * @param object $migration
     * @param string $method
     * @return void
     */
    protected function pretendToRun($migration, $method) {
        foreach($this->getQueries($migration, $method) as $query) {
            $name = get_class($migration);
            $this->note("<info>{$name}:</info> {$query['query']}");
        }
    }
    /**
     * @param object $migration
     * @param string $method
     * @return array
     */
    protected function getQueries($migration, $method) {
        $connection = $migration->getConnection();
        $db = $this->resolveConnection($connection);
        return $db->pretend(function () use ($migration, $method) {
            $migration->$method();
        });
    }
    /**
     * @param string $file
     * @return object
     */
    public function resolve($file) {
        $file = implode('_', array_slice(explode('_', $file), 4));
        $class = Str::studly($file);
        return $this->application->make($class);
    }
    /**
     * @param string $message
     * @return void
     */
    protected function note($message) {
        $this->notes[] = $message;
    }
    /**
     * @return array
     */
    public function getNotes() {
        return $this->notes;
    }
    /**
     * @param string $connection
     * @return \Notadd\Foundation\Database\Connection
     */
    public function resolveConnection($connection) {
        return $this->resolver->connection($connection);
    }
    /**
     * @param string $name
     * @return void
     */
    public function setConnection($name) {
        if(!is_null($name)) {
            $this->resolver->setDefaultConnection($name);
        }
        $this->repository->setSource($name);
        $this->connection = $name;
    }
    /**
     * @return \Notadd\Foundation\Database\Migrations\MigrationRepositoryInterface
     */
    public function getRepository() {
        return $this->repository;
    }
    /**
     * @return bool
     */
    public function repositoryExists() {
        return $this->repository->repositoryExists();
    }
    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem() {
        return $this->files;
    }
}