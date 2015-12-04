<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-27 23:17
 */
namespace Notadd\Install\Controllers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Notadd\Foundation\Routing\Controller;
use Notadd\Install\Console\InstallCommand;
use Notadd\Install\Requests\InstallRequest;
class InstallController extends Controller {
    /**
     * @var \Notadd\Install\Console\InstallCommand
     */
    protected $command;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Notadd\Install\Console\InstallCommand $command
     */
    public function __construct(Application $app, Factory $view, InstallCommand $command) {
        parent::__construct($app, $view);
        $this->command = $command;
    }
    /**
     * @param \Notadd\Install\Requests\InstallRequest $request
     */
    public function handle(InstallRequest $request) {
    }
}