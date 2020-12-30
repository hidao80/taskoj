<?php

class AuthController extends BaseController {

	public function login()
	{
        $inputs = Input::only('username', 'password', 'team');
        Log::debug($inputs);
        $authResult = Auth::attempt($inputs) ? "true" : "false";
        $log = 'auth::attempt($input) = '. $authResult;
        Log::debug($log);
        if ( Auth::attempt($inputs) ) {
            return Redirect::to( '/hello' );
        }
        return Redirect::back()->withInput();
	}

	public function logout()
    {
        Auth::logout();
        return Redirect::to( 'login' );
    }

}
