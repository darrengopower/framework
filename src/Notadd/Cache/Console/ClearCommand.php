<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-27 23:15
 */
namespace Notadd\Cache\Console;
use Illuminate\Cache\CacheManager;
use Notadd\Foundation\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
/**
 * Class ClearCommand
 * @package Notadd\Cache\Console
 */
class ClearCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'cache:clear';
    /**
     * @var string
     */
    protected $description = 'Flush the application cache';
    /**
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;
    /**
     * ClearCommand constructor.
     * @param \Illuminate\Cache\CacheManager $cache
     */
    public function __construct(CacheManager $cache) {
        parent::__construct();
        $this->cache = $cache;
    }
    /**
     * @return void
     */
    public function fire() {
        $storeName = $this->argument('store');
        $this->notadd['events']->fire('cache:clearing', [$storeName]);
        $this->cache->store($storeName)->flush();
        $this->notadd['events']->fire('cache:cleared', [$storeName]);
        $this->info('Application cache cleared!');
    }
    /**
     * @return array
     */
    protected function getArguments() {
        return [
            [
                'store',
                InputArgument::OPTIONAL,
                'The name of the store you would like to clear.'
            ],
        ];
    }
}