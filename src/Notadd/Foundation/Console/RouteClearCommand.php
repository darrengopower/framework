<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:17
 */
namespace Notadd\Foundation\Console;
use Illuminate\Filesystem\Filesystem;
class RouteClearCommand extends Command {
    protected $name = 'route:clear';
    protected $description = 'Remove the route cache file';
    protected $files;
    public function __construct(Filesystem $files) {
        parent::__construct();
        $this->files = $files;
    }
    public function fire() {
        $this->files->delete($this->notadd->getCachedRoutesPath());
        $this->info('Route cache cleared!');
    }
}