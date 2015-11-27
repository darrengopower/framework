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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
class RedirectIfAuthenticated {
    /**
     * @var Guard
     */
    protected $auth;
    /**
     * @param  Guard $auth
     */
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }
    /**
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($this->auth->check()) {
            return new RedirectResponse(UrlGenerator::to('admin'));
        }
        return $next($request);
    }
}