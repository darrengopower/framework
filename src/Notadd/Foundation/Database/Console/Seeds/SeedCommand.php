<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:13
 */
namespace Notadd\Foundation\Database\Console\Seeds;
use Notadd\Foundation\Console\Command;
use Notadd\Foundation\Database\ConnectionResolverInterface as Resolver;
use Symfony\Component\Console\Input\InputOption;
class SeedCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'db:seed';
    /**
     * @var string
     */
    protected $description = 'Seed the database with records';
    /**
     * @var \Notadd\Foundation\Database\ConnectionResolverInterface
     */
    protected $resolver;
    /**
     * @param \Notadd\Foundation\Database\ConnectionResolverInterface $resolver
     */
    public function __construct(Resolver $resolver) {
        parent::__construct();
        $this->resolver = $resolver;
    }
    /**
     * @return void
     */
    public function fire() {
        $this->resolver->setDefaultConnection($this->getDatabase());
        $this->getSeeder()->run();
    }
    /**
     * @return \Notadd\Foundation\Database\Seeder
     */
    protected function getSeeder() {
        $class = $this->notadd->make($this->input->getOption('class'));
        return $class->setContainer($this->notadd)->setCommand($this);
    }
    /**
     * @return string
     */
    protected function getDatabase() {
        $database = $this->input->getOption('database');
        return $database ?: $this->notadd['config']['database.default'];
    }
    /**
     * @return array
     */
    protected function getOptions() {
        return [
            [
                'class',
                null,
                InputOption::VALUE_OPTIONAL,
                'The class name of the root seeder',
                'DatabaseSeeder'
            ],
            [
                'database',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connection to seed'
            ],
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production.'
            ],
        ];
    }
}