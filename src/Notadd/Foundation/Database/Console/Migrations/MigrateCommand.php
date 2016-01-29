<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 00:59
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
/**
 * Class MigrateCommand
 * @package Notadd\Foundation\Database\Console\Migrations
 */
class MigrateCommand extends BaseCommand {
    /**
     * @var string
     */
    protected $name = 'migrate';
    /**
     * @var string
     */
    protected $description = 'Run the database migrations';
    /**
     * @var \Notadd\Foundation\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
     * MigrateCommand constructor.
     * @param \Notadd\Foundation\Database\Migrations\Migrator $migrator
     */
    public function __construct(Migrator $migrator) {
        parent::__construct();
        $this->migrator = $migrator;
    }
    /**
     * @return void
     */
    public function fire() {
        $this->prepareDatabase();
        $pretend = $this->input->getOption('pretend');
        if(!is_null($path = $this->input->getOption('path'))) {
            $path = $this->notadd->basePath() . '/' . $path;
        } else {
            $path = $this->getMigrationPath();
        }
        $this->migrator->run($path, $pretend);
        foreach($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
        if($this->input->getOption('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }
    /**
     * @return void
     */
    protected function prepareDatabase() {
        $this->migrator->setConnection($this->input->getOption('database'));
        if(!$this->migrator->repositoryExists()) {
            $options = ['--database' => $this->input->getOption('database')];
            $this->call('migrate:install', $options);
        }
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
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production.'
            ],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The path of migrations files to be executed.'
            ],
            [
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run.'
            ],
            [
                'seed',
                null,
                InputOption::VALUE_NONE,
                'Indicates if the seed task should be re-run.'
            ],
        ];
    }
}