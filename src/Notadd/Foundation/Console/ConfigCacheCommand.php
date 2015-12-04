<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:02
 */
namespace Notadd\Foundation\Console;
use Illuminate\Filesystem\Filesystem;
class ConfigCacheCommand extends Command {
    protected $name = 'config:cache';
    protected $description = 'Create a cache file for faster configuration loading';
    protected $files;
    public function __construct(Filesystem $files) {
        parent::__construct();
        $this->files = $files;
    }
    public function fire() {
        $this->call('config:clear');
        $config = $this->getFreshConfiguration();
        $this->files->put($this->notadd->getCachedConfigPath(), '<?php return ' . var_export($config, true) . ';' . PHP_EOL);
        $this->info('Configuration cached successfully!');
    }
    protected function getFreshConfiguration() {
        $app = require $this->notadd->basePath() . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        return $app['config']->all();
    }
}