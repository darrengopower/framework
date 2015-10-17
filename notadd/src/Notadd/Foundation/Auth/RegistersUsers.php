<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Foundation\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
trait RegistersUsers {
    use RedirectsUsers;
    /**
     * @return \Illuminate\Http\Response
     */
    public function getRegister() {
        return view('auth.register');
    }
    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request) {
        $validator = $this->validator($request->all());
        if($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
        Auth::login($this->create($request->all()));
        return redirect($this->redirectPath());
    }
}