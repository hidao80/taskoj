<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends BaseController {

	public function create()
	{
        $inputs = Input::only('username', 'password', 'team', 'user_rank');
        $inputs['user_rank'] = intval($inputs['user_rank']);
        Log::debug($inputs);

        $rules = array(
        	'username' => array('required', 'min:1', 'max:20', 'unique:users'),
        	'password' => array('required', 'min:8', 'max:64'),
        	'team' => array('required', 'min:1', 'max:64'),
        	'user_rank' => array('required', 'numeric', 'between:'. Auth::user()->user_rank .',9'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
	        $user = User::firstOrCreate( array(
	            'username' => $inputs['username'],
	            'password' => $inputs['password'],
	            'team' => $inputs['team'],
	            'remember_token' => "",
	            'user_rank' => $inputs['user_rank'],
	        ) );
			Log::debug($user);
		}
		
	    return Redirect::back()->with('msg', "ユーザ ${inputs['username']} を登録しました。");
	}

	public function update()
	{
        $inputs = Input::only('username', 'password', 'team', 'user_rank');
        $inputs['user_rank'] = intval($inputs['user_rank']);
        Log::debug($inputs);
        
        $rules = array(
        	'username' => array('required', 'min:1', 'max:20', 'exists:users'),
        	'password' => array('required', 'min:8', 'max:64'),
        	'team' => array('required', 'min:1', 'max:64'),
        	'user_rank' => array('required', 'numeric', 'between:'. Auth::user()->user_rank .',9'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$user = User::where('username', '=', $inputs['username'])->firstOrFail();

		        $updateData = Input::only('username', 'password', 'team', 'user_rank');
				$user = $user->fill($updateData)->save();
				
				Log::debug($user);
	
			    return Redirect::back()->with('msg', "ユーザ ${inputs['username']} を更新しました。");
			} catch (ModelNotFoundException $e) {
				// バリデーションされているのでここは通らないはず
				$this->create();
			}
		}
	}

	public function delete()
    {
        $inputs = Input::only('username', 'team');
        Log::debug($inputs);
        
        $rules = array(
        	'username' => array('required', 'min:1', 'max:20', 'exists:users'),
        	'team' => array('required', 'min:1', 'max:64'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$user = User::where('username', '=', $inputs['username'])->where('team', '=', $inputs['team'])->firstOrFail();
				Log::debug($user);
				
				// 上位の権限を持つユーザは削除させない
				if ($user->user_rank < Auth::user()->user_rank) {
			        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['username']} を削除する権限がありません。"]);
				}

                if ($user->delete()){
				    return Redirect::back()->with('msg', "ユーザ ${inputs['username']} を削除しました。");
                }   
				else {
			        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['username']} の削除に失敗しました。"]);
				}
			} catch (ModelNotFoundException $e) {
				// バリデーションされているのでここは通らないはず
		        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['username']} は存在しません。"]);
			}
        }
    }

}
