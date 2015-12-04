<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:05
 */
namespace Notadd\Foundation\Console;
use Illuminate\Filesystem\Filesystem;
class ConfigClearCommand extends Command {
    protected $name = 'config:clear';
    protected $description = 'Remove the configuration cache file';
    protected $files;
    public function __construct(Filesystem $files) {
        parent::__construct();
        $this->files = $files;
    }
    public function fire() {
        $this->files->delete($this->notadd->getCachedConfigPath());
        $this->info('Configuration cache cleared!');
    }
}