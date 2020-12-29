<?php

class AuthController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

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
