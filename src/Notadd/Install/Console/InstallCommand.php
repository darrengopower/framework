<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-29 00:50
 */
namespace Notadd\Install\Console;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Notadd\Foundation\Console\Command;
class InstallCommand extends Command {
    protected $application;
    protected $dataSource;
    protected $filesystem;
    protected $name = 'install';
    protected $description = 'Application Installation';
    public function __construct(Application $application, Filesystem $filesystem) {
        $this->application = $application;
        parent::__construct();
        $this->filesystem = $filesystem;
    }
    public function fire() {
    }
}