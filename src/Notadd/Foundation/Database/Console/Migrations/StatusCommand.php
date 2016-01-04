<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:12
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
/**
 * Class StatusCommand
 * @package Notadd\Foundation\Database\Console\Migrations
 */
class StatusCommand extends BaseCommand {
    /**
     * @var string
     */
    protected $name = 'migrate:status';
    /**
     * @var string
     */
    protected $description = 'Show the status of each migration';
    /**
     * @var \Notadd\Foundation\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
     * StatusCommand constructor.
     * @param \Notadd\Foundation\Database\Migrations\Migrator $migrator
     */
    public function __construct(Migrator $migrator) {
        parent::__construct();
        $this->migrator = $migrator;
    }
    /**
     * Execute the console command.
     * @return void
     */
    public function fire() {
        if(!$this->migrator->repositoryExists()) {
            return $this->error('No migrations found.');
        }
        $this->migrator->setConnection($this->input->getOption('database'));
        if(!is_null($path = $this->input->getOption('path'))) {
            $path = $this->notadd->basePath() . '/' . $path;
        } else {
            $path = $this->getMigrationPath();
        }
        $ran = $this->migrator->getRepository()->getRan();
        $migrations = [];
        foreach($this->getAllMigrationFiles($path) as $migration) {
            $migrations[] = in_array($migration, $ran) ? [
                '<info>Y</info>',
                $migration
            ] : [
                '<fg=red>N</fg=red>',
                $migration
            ];
        }
        if(count($migrations) > 0) {
            $this->table([
                'Ran?',
                'Migration'
            ], $migrations);
        } else {
            $this->error('No migrations found');
        }
    }
    /**
     * @param string $path
     * @return array
     */
    protected function getAllMigrationFiles($path) {
        return $this->migrator->getMigrationFiles($path);
    }
    /**
     * @return array
     */
    protected function getOptions() {
        return [
            [
                'database',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connection to use.'
            ],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The path of migrations files to use.'
            ],
        ];
    }
}