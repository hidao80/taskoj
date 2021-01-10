<?php

class AuthController extends BaseController {

	public function login()
	{
        $inputs = Input::only('user_name', 'password', 'team');
        Log::debug($inputs);

        $authResult = Auth::attempt($inputs) ? "true" : "false";
        Log::debug('auth::attempt($input) = '. $authResult);

        if ( $authResult === 'true' ) {
            return Redirect::to( 'task/list' );
        }
        return Redirect::back()->withErrors(['ログイン認証に失敗しました。'])->withInput();
	}

	public function logout()
    {
        Auth::logout();
        return Redirect::to( 'login' );
    }

}
