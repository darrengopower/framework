<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:09
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
use Notadd\Foundation\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
class ResetCommand extends Command {
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'migrate:reset';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rollback all database migrations';
    /**
     * The migrator instance.
     * @var \Notadd\Foundation\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
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
        $this->migrator->setConnection($this->input->getOption('database'));
        if(!$this->migrator->repositoryExists()) {
            $this->output->writeln('<comment>Migration table not found.</comment>');
            return;
        }
        $pretend = $this->input->getOption('pretend');
        $this->migrator->reset($pretend);
        foreach($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
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
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run.'
            ],
        ];
    }
}