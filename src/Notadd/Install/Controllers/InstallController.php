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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\StreamOutput;
class InstallController extends Controller {
    /**
     * @var \Notadd\Install\Console\InstallCommand
     */
    protected $command;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Application $app, Factory $view) {
        parent::__construct($app, $view);
        $this->command = $this->getCommand('install');
    }
    /**
     * @param \Notadd\Install\Requests\InstallRequest $request
     */
    public function handle(InstallRequest $request) {
        $output = new BufferedOutput();
        $this->command->setDataFromCalling($request);
        $this->command->run(new ArrayInput([]), $output);
        echo $output->fetch();
    }
}