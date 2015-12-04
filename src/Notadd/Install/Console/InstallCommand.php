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
use Illuminate\Support\Collection;
use Notadd\Foundation\Console\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
class InstallCommand extends Command {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $application;
    /**
     * @var static
     */
    protected $data;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;
    /**
     * @var bool
     */
    protected $isDataSetted = false;
    /**
     * @var string
     */
    protected $name = 'install';
    /**
     * @var string
     */
    protected $description = '应用程序安装器';
    /**
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Application $application, Filesystem $filesystem) {
        parent::__construct();
        $this->application = $application;
        $this->data = Collection::make();
        $this->filesystem = $filesystem;
    }
    public function fire() {
        if(!$this->isDataSetted) {
            $this->setDataFromConsoling();
        }
    }
    public function setDataFromCalling($request) {
        $this->data->put('driver', 'mysql');
        $this->data->put('host', $request->offset('host'));
        $this->data->put('database', $request->offset('database'));
        $this->data->put('username', $request->offset('username'));
        $this->data->put('password', $request->offset('password'));
        $this->data->put('prefix', $request->offset('prefix'));
        $this->data->put('admin_username', $request->offset('username'));
        $this->data->put('admin_password', $request->offset('password'));
        $this->data->put('admin_password_confirmation', $request->offset('password_confirmation'));
        $this->data->put('admin_email', $request->offset('email'));
        $this->data->put('title', $request->offset('title'));
        $this->isDataSetted = true;
    }
    public function setDataFromConsoling() {
        $this->data->put('driver', 'mysql');
        $this->data->put('host', $this->ask('数据库服务器：'));
        $this->data->put('database', $this->ask('数据库名：'));
        $this->data->put('username', $this->ask('数据库用户名：'));
        $this->data->put('password', $this->secret('数据库密码：'));
        $this->data->put('prefix', $this->ask('数据库表前缀：'));
        $this->data->put('admin_username', $this->ask('管理员帐号：'));
        $this->data->put('admin_password', $this->secret('管理员密码：'));
        $this->data->put('admin_password_confirmation', $this->secret('重复密码：'));
        $this->data->put('admin_email', $this->ask('电子邮箱：'));
        $this->data->put('title', $this->ask('网站标题：'));
        $this->isDataSetted = true;
    }
}