<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-27 21:30
 */
namespace Notadd\Cache\Controllers\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Foundation\Console\Kernel;
use Notadd\Foundation\SearchEngine\Optimization;
use Notadd\Setting\Factory;
/**
 * Class CacheController
 * @package Notadd\Cache\Controllers\Admin
 */
class CacheController extends AbstractAdminController {
    /**
     * @var \Notadd\Foundation\Console\Kernel
     */
    protected $artisan;
    /**
     * CacheController constructor.
     * @param \Notadd\Foundation\Console\Kernel $artisan
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Events\Dispatcher $events
     * @param \Illuminate\Routing\Redirector $redirect
     * @param \Illuminate\Http\Request $request
     * @param \Notadd\Setting\Factory $setting
     * @param \Notadd\Foundation\SearchEngine\Optimization $seo
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Kernel $artisan, Application $app, Dispatcher $events, Redirector $redirect, Request $request, Factory $setting, Optimization $seo, ViewFactory $view) {
        parent::__construct($app, $events, $redirect, $request, $setting, $seo, $view);
        $this->artisan = $artisan;
    }
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index() {
        return $this->view('cache.index');
    }
    public function clearCache() {
        $this->artisan->call('cache:clear');
        return $this->redirect->back();
    }
    public function clearStatic() {
        return $this->redirect->back();
    }
    public function clearView() {
        $this->artisan->call('view:clear');
        return $this->redirect->back();
    }
}