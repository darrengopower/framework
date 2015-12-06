<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 12:54
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
use Symfony\Component\Console\Input\InputOption;
class InstallCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'migrate:install';
    /**
     * @var string
     */
    protected $description = 'Create the migration repository';
    /**
     * @var \Notadd\Foundation\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;
    /**
     * @param \Notadd\Foundation\Database\Console\Migrations\MigrationRepositoryInterface $repository
     */
    public function __construct(MigrationRepositoryInterface $repository) {
        parent::__construct();
        $this->repository = $repository;
    }
    /**
     * @return void
     */
    public function fire() {
        $this->repository->setSource($this->input->getOption('database'));
        $this->repository->createRepository();
        $this->info('Migration table created successfully.');
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
        ];
    }
}