<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends BaseController {

	public function create()
	{
		Log::debug('enter: '.__FUNCTION__);
		
        $inputs = Input::only('user_name', 'display_name', 'password', 'team', 'user_rank');
        $inputs['user_rank'] = intval($inputs['user_rank']);
        Log::debug($inputs);

        $rules = array(
        	'user_name' => array('required', 'min:1', 'max:20', 'unique:users'),
        	'password' => array('required', 'min:8', 'max:64'),
        	'display_name' => array('required', 'min:1', 'max:60'),
        	'team' => array('required', 'min:1', 'max:64'),
        	'user_rank' => array('required', 'numeric', 'between:'. Auth::user()->user_rank .',9'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
	        $user = User::firstOrCreate( array(
	            'user_name' => $inputs['user_name'],
	            'display_name' => $inputs['display_name'],
	            'password' => $inputs['password'],
	            'team' => $inputs['team'],
	            'remember_token' => "",
	            'user_rank' => $inputs['user_rank'],
	        ) );
			Log::debug($user);
		}
		
	    return Redirect::back()->with('msg', "ユーザ ${inputs['user_name']} を登録しました。");
	}

	public function update()
	{
		Log::debug('enter: '.__FUNCTION__);

        $inputs = Input::only('user_name', 'display_name', 'password', 'team', 'user_rank');
        $inputs['user_rank'] = intval($inputs['user_rank']);
        Log::debug($inputs);
        
        $rules = array(
        	'user_name' => array('required', 'min:1', 'max:20', 'exists:users'),
        	'password' => array('required', 'min:8', 'max:64'),
        	'display_name' => array('required', 'min:1', 'max:60'),
        	'team' => array('required', 'min:1', 'max:64'),
        	'user_rank' => array('required', 'numeric', 'between:'. Auth::user()->user_rank .',9'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$user = User::where('user_name', '=', $inputs['user_name'])->firstOrFail();

		        $updateData = Input::only('user_name', 'display_name', 'password', 'team', 'user_rank');
				$user = $user->fill($updateData)->save();
				
				Log::debug($user);
	
			    return Redirect::back()->with('msg', "ユーザ ${inputs['user_name']} を更新しました。");
			} catch (ModelNotFoundException $e) {
				// バリデーションされているのでここは通らないはず
				$this->create();
			}
		}
	}

	public function show($id)
	{
		Log::debug('enter: '.__FUNCTION__);

        $user = User::find($id);
        if ($user == null) {
            $user_rank = Auth::user()->user_rank;
            $user_name = Auth::user()->user_name;
            $display_name = Auth::user()->display_name;
            $team = Auth::user()->team;
            $msg = trans('taskoj.user_not_found', ['user_id' => $id]);
        }
        else {
            $user_rank = $user->user_rank;
            $user_name = $user->user_name;
            $display_name = $user->display_name;
            $team = $user->team;
            $msg = '';
        }
        return View::make( 'user/update', [
            'user_rank' => $user_rank, 
            'user_name' => $user_name,
            'display_name' => $display_name,
            'team' => $team,
            'msg' => $msg,
            ]);
	}

	public function delete()
    {
		Log::debug('enter: '.__FUNCTION__);
		
        $inputs = Input::only('user_name', 'team');
        Log::debug($inputs);
        
        $rules = array(
        	'user_name' => array('required', 'min:1', 'max:20', 'exists:users'),
        	'team' => array('required', 'min:1', 'max:64'),
        	);

		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$user = User::where('user_name', '=', $inputs['user_name'])->where('team', '=', $inputs['team'])->firstOrFail();
				Log::debug($user);
				
				// 上位の権限を持つユーザは削除させない
				if ($user->user_rank < Auth::user()->user_rank) {
			        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['user_name']} を削除する権限がありません。"]);
				}

                if ($user->delete()){
				    return Redirect::back()->with('msg', "ユーザ ${inputs['user_name']} を削除しました。");
                }   
				else {
			        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['user_name']} の削除に失敗しました。"]);
				}
			} catch (ModelNotFoundException $e) {
				// バリデーションされているのでここは通らないはず
		        return Redirect::back()->withErrors(['msg' => "ユーザ ${inputs['user_name']} は存在しません。"]);
			}
        }
    }

}
