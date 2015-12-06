<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:08
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
use Symfony\Component\Console\Input\InputOption;
class RefreshCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'migrate:refresh';
    /**
     * @var string
     */
    protected $description = 'Reset and re-run all migrations';
    /**
     * @return void
     */
    public function fire() {
        $database = $this->input->getOption('database');
        $force = $this->input->getOption('force');
        $path = $this->input->getOption('path');
        $this->call('migrate:reset', [
            '--database' => $database,
            '--force' => $force,
        ]);
        $this->call('migrate', [
            '--database' => $database,
            '--force' => $force,
            '--path' => $path,
        ]);
        if($this->needsSeeding()) {
            $this->runSeeder($database);
        }
    }
    /**
     * @return bool
     */
    protected function needsSeeding() {
        return $this->option('seed') || $this->option('seeder');
    }
    /**
     * @param string $database
     * @return void
     */
    protected function runSeeder($database) {
        $class = $this->option('seeder') ?: 'DatabaseSeeder';
        $force = $this->input->getOption('force');
        $this->call('db:seed', [
            '--database' => $database,
            '--class' => $class,
            '--force' => $force,
        ]);
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
                'seed',
                null,
                InputOption::VALUE_NONE,
                'Indicates if the seed task should be re-run.'
            ],
            [
                'seeder',
                null,
                InputOption::VALUE_OPTIONAL,
                'The class name of the root seeder.'
            ],
        ];
    }
}