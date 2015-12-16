<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-20 18:44
 */
namespace Notadd\Admin\Controllers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Notadd\Foundation\Auth\ResetsPasswords;
class PasswordController extends AbstractAdminController {
    use ResetsPasswords;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Application $app, Factory $view, Request $request) {
        parent::__construct($app, $request, $view);
        $this->middleware('guest.admin');
    }
}