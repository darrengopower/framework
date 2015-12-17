<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-20 18:44
 */
namespace Notadd\Admin\Controllers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Notadd\Foundation\Auth\ResetsPasswords;
use Notadd\Foundation\SearchEngine\Optimization;
use Notadd\Setting\Factory as SettingFactory;
class PasswordController extends AbstractAdminController {
    use ResetsPasswords;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Events\Dispatcher $events
     * @param \Illuminate\Routing\Redirector $redirect
     * @param \Illuminate\Http\Request $request
     * @param \Notadd\Setting\Factory $setting
     * @param \Notadd\Foundation\SearchEngine\Optimization $seo
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Application $app, Dispatcher $events, Redirector $redirect, Request $request, SettingFactory $setting, Optimization $seo, ViewFactory $view) {
        parent::__construct($app, $events, $redirect, $request, $setting, $seo, $view);
        $this->middleware('guest.admin');
    }
}