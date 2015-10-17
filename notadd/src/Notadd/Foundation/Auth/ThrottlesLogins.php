<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Foundation\Auth;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Lang;
trait ThrottlesLogins {
    /**
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request) {
        return app(RateLimiter::class)->tooManyAttempts($request->input($this->loginUsername()) . $request->ip(), $this->maxLoginAttempts(), $this->lockoutTime() / 60);
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return int
     */
    protected function incrementLoginAttempts(Request $request) {
        app(RateLimiter::class)->hit($request->input($this->loginUsername()) . $request->ip());
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request) {
        $seconds = app(RateLimiter::class)->availableIn($request->input($this->loginUsername()) . $request->ip());
        return redirect($this->loginPath())->withInput($request->only($this->loginUsername(), 'remember'))->withErrors([
                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
            ]);
    }
    /**
     * @param  int $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds) {
        return Lang::has('auth.throttle') ? Lang::get('auth.throttle', ['seconds' => $seconds]) : 'Too many login attempts. Please try again in ' . $seconds . ' seconds.';
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request) {
        app(RateLimiter::class)->clear($request->input($this->loginUsername()) . $request->ip());
    }
    /**
     * @return int
     */
    protected function maxLoginAttempts() {
        return property_exists($this, 'maxLoginAttempts') ? $this->maxLoginAttempts : 5;
    }
    /**
     * @return int
     */
    protected function lockoutTime() {
        return property_exists($this, 'lockoutTime') ? $this->lockoutTime : 60;
    }
}