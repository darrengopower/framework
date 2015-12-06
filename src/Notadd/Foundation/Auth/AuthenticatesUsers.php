<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
trait AuthenticatesUsers {
    use RedirectsUsers;
    /**
     * @return \Illuminate\Http\Response
     */
    public function getLogin() {
        if(view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }
        return view('auth.login');
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request) {
        $this->validate($request, [
            $this->loginUsername() => 'required',
            'password' => 'required',
        ]);
        $throttles = $this->isUsingThrottlesLoginsTrait();
        if($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->getCredentials($request);
        if(Auth::attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }
        if($throttles) {
            $this->incrementLoginAttempts($request);
        }
        return redirect($this->loginPath())->withInput($request->only($this->loginUsername(), 'remember'))->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @param bool $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles) {
        if($throttles) {
            $this->clearLoginAttempts($request);
        }
        if(method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }
        return redirect()->intended($this->redirectPath());
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function getCredentials(Request $request) {
        return $request->only($this->loginUsername(), 'password');
    }
    /**
     * @return string
     */
    protected function getFailedLoginMessage() {
        return Lang::has('auth.failed') ? Lang::get('auth.failed') : 'These credentials do not match our records.';
    }
    /**
     * @return \Illuminate\Http\Response
     */
    public function getLogout() {
        Auth::logout();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
    /**
     * @return string
     */
    public function loginPath() {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }
    /**
     * @return string
     */
    public function loginUsername() {
        return property_exists($this, 'username') ? $this->username : 'email';
    }
    /**
     * @return bool
     */
    protected function isUsingThrottlesLoginsTrait() {
        return in_array(ThrottlesLogins::class, class_uses_recursive(get_class($this)));
    }
}