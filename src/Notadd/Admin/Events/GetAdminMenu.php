<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-25 22:33
 */
namespace Notadd\Admin\Events;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
class GetAdminMenu {
    protected $application;
    protected $config;
    public function __construct(Application $application, Repository $config) {
        $this->application = $application;
        $this->config = $config;
    }
    public function addMenu($key, $value) {
        $key = 'admin.' . $key;
        $this->config->set($key, $value);
    }
}