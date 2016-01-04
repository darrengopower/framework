<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 00:47
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Composer\Composer;
use Notadd\Foundation\Database\Migrations\MigrationCreator;
/**
 * Class MigrateMakeCommand
 * @package Notadd\Foundation\Database\Console\Migrations
 */
class MigrateMakeCommand extends BaseCommand {
    /**
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}
        {--path= : The location where the migration file should be created.}';
    /**
     * @var string
     */
    protected $description = 'Create a new migration file';
    /**
     * @var \Notadd\Foundation\Database\Migrations\MigrationCreator
     */
    protected $creator;
    /**
     * @var \Notadd\Foundation\Composer\Composer
     */
    protected $composer;
    /**
     * MigrateMakeCommand constructor.
     * @param \Notadd\Foundation\Database\Migrations\MigrationCreator $creator
     * @param \Notadd\Foundation\Composer\Composer $composer
     */
    public function __construct(MigrationCreator $creator, Composer $composer) {
        parent::__construct();
        $this->creator = $creator;
        $this->composer = $composer;
    }
    /**
     * @return void
     */
    public function fire() {
        $name = $this->input->getArgument('name');
        $table = $this->input->getOption('table');
        $create = $this->input->getOption('create');
        if(!$table && is_string($create)) {
            $table = $create;
        }
        $this->writeMigration($name, $table, $create);
        $this->composer->dumpAutoloads();
    }
    /**
     * @param string $name
     * @param string $table
     * @param bool $create
     * @return string
     */
    protected function writeMigration($name, $table, $create) {
        $path = $this->getMigrationPath();
        $file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);
        $this->line("<info>Created Migration:</info> $file");
    }
    /**
     * @return string
     */
    protected function getMigrationPath() {
        if(!is_null($targetPath = $this->input->getOption('path'))) {
            return $this->notadd->basePath() . '/' . $targetPath;
        }
        return parent::getMigrationPath();
    }
}