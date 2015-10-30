<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-29 22:45
 */
namespace Notadd\Admin\Controllers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notadd\Foundation\Routing\Controller;
class AbstractAdminController extends Controller {
    public function __construct(Factory $view, Request $request) {
        parent::__construct($view);
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