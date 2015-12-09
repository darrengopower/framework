<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 22:45
 */
namespace Notadd\Admin\Controllers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notadd\Foundation\Routing\Controller;
class AbstractAdminController extends Controller {
    /**
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirect;
    /**
     * @var \Illuminate\Session\SessionManager
     */
    protected $session;
    /**
     * @var \Notadd\Setting\Factory
     */
    protected $setting;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Application $app, Factory $view, Request $request) {
        parent::__construct($app, $view);
        $this->redirect = $app->make('redirect');
        $this->session = $app->make('session');
        $this->setting = $app->make('setting');
        $this->share('admin_theme', $request->cookie('admin-theme'));
    }
    /**
     * @param $template
     * @return \Illuminate\Contracts\View\View
     */
    protected function view($template) {
        if(Str::contains($template, '::')) {
            return $this->view->make($template);
        } else {
            return $this->view->make('admin::' . $template);
        }
    }
}