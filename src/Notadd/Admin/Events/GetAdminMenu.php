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
/**
 * Class GetAdminMenu
 * @package Notadd\Admin\Events
 */
class GetAdminMenu {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $application;
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;
    /**
     * GetAdminMenu constructor.
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Application $application, Repository $config) {
        $this->application = $application;
        $this->config = $config;
    }
    /**
     * @param $key
     * @param $value
     */
    public function addMenu($key, $value) {
        $key = 'admin.' . $key;
        $this->config->set($key, $value);
    }
}