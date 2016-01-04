<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:11
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
use Notadd\Foundation\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
/**
 * Class RollbackCommand
 * @package Notadd\Foundation\Database\Console\Migrations
 */
class RollbackCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'migrate:rollback';
    /**
     * @var string
     */
    protected $description = 'Rollback the last database migration';
    /**
     * @var \Notadd\Foundation\Database\Migrations\Migrator
     */
    protected $migrator;
    /**
     * RollbackCommand constructor.
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
        $this->migrator->setConnection($this->input->getOption('database'));
        $pretend = $this->input->getOption('pretend');
        $this->migrator->rollback($pretend);
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