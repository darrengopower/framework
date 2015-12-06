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
class AuthenticateWithAdmin {
    /**
     * The Guard implementation.
     * @var Guard
     */
    protected $auth;
    /**
     * @param Guard $auth
     */
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
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