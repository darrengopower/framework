<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Admin\Middleware;
use Closure;
use Illuminate\Contracts\Auth\Guard;
/**
 * Class AuthenticateWithAdmin
 * @package Notadd\Admin\Middleware
 */
class AuthenticateWithAdmin {
    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;
    /**
     * AuthenticateWithAdmin constructor.
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }
    /**
     * @param $request
     * @param \Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next) {
        if($this->auth->guest()) {
            if($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('admin/login');
            }
        }
        return $next($request);
    }
}