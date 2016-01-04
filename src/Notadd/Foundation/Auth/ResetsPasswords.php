<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Auth;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * Class ResetsPasswords
 * @package Notadd\Foundation\Auth
 */
trait ResetsPasswords {
    /**
     * @return \Illuminate\Http\Response
     */
    public function getEmail() {
        return view('admin::auth.password');
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request) {
        $this->validate($request, ['email' => 'required|email']);
        $response = $this->app->make('auth.password')->sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });
        switch($response) {
            case 'passwords.sent':
                return redirect()->back()->with('status', trans($response));
            case 'passwords.user':
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }
    /**
     * @return string
     */
    protected function getEmailSubject() {
        return isset($this->subject) ? $this->subject : 'Your Password Reset Link';
    }
    /**
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function getReset($token = null) {
        if(is_null($token)) {
            throw new NotFoundHttpException;
        }
        return view('admin::auth.reset')->with('token', $token);
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request) {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        $response = $this->app->make('auth.password')->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        switch($response) {
            case 'passwords.reset':
                return redirect($this->redirectPath())->with('status', trans($response));
            default:
                return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response)]);
        }
    }
    /**
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password) {
        $user->password = bcrypt($password);
        $user->save();
        $this->app->make('auth')->login($user);
    }
    /**
     * @return string
     */
    public function redirectPath() {
        if(property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : 'admin';
    }
}