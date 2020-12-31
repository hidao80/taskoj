<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends BaseController {

	public function get()
	{
        $inputs = Input::only('task_id');
    	$inputs['task_id'] = intval($inputs['task_id']);

		Validator::extend('nullableExists', 'TaskController@validationNullableExists');

        $rules = array(
        	'task_id' => array('nullableExists:tasks,id'),
			);
			
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
			Session::set('msg', "タスクID ${inputs['task_id']} は存在しません。");
        	return View::make( 'task_edit', [
                'task_id' => $inputs['task_id'], 
                'user' => Auth::user(), 
                'team_info' => array('staff' => User::where('team', Auth::user()->team)->get(), 'staff_id' => []),
                'record' => null,
                ]);
		}
		else {
			$record = Task::find($inputs['task_id']);
			
			if ( count($record) <= 0 ) {
				$record = null;
			}
			
			Log::debug($record);
            return View::make( 'task_edit', [
                'task_id' => $inputs['task_id'], 
                'user' => Auth::user(), 
                'team_info' => array('staff' => User::where('team', Auth::user()->team)->get(), 'staff_id' => []),
                'record' => $record,
                ]);
		}
	}

	public function getId($id)
	{
    	$inputs['task_id'] = intval($id, 10);

        $rules = array(
        	'task_id' => array('exists:tasks,id'),
			);
			
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
			Session::set('msg', "タスクID ${inputs['task_id']} は存在しません。");
        	return View::make( 'task_edit', [
                'task_id' => $inputs['task_id'], 
                'user' => Auth::user(), 
                'team_info' => User::where('team', Auth::user()->team)->get(),
                'record' => null,
                ]);
		}
		else {
			$record = Task::find($inputs['task_id']);
			
			if ( count($record) <= 0 ) {
				$record = null;
			}
			
			$user_ids = TaskStaff::where('task_id', $inputs['task_id'])->lists('user_id');

			Log::debug($record);
			Session::set('msg', '');
            return View::make( 'task_edit', [
                'task_id' => $inputs['task_id'], 
                'user' => Auth::user(), 
                'team_info' => array('staff' => User::where('team', Auth::user()->team)->get(), 'staff_id' => $user_ids),
                'record' => $record,
                ]);
		}
	}

	public function edit()
	{
        $inputs = Input::all();
        $inputs['priority'] = intval($inputs['priority']);
        $inputs['time_span'] = floatval($inputs['time_span']);
        $inputs['user_rank'] = intval($inputs['user_rank']);
        $inputs['task_id'] = isset($inputs['task_id']) ? intval($inputs['task_id']) : null;
        $inputs['parent_task_id'] = isset($inputs['parent_task_id']) ? intval($inputs['parent_task_id']) : null;
        Log::debug($inputs);

		Validator::extend('nullableExists', 'TaskController@validationNullableExists');

        $rules = array(
        	'done' => array('booealn'),
        	'title' => array('required', 'between:1,255', 'string'),
        	'deadline' => array('date'),
        	'priority' => array('required', 'integer', 'min:1'),
        	'note' => array('string'),
        	'time_span' => array('numeric', 'min:0'),
        	'delete' => array('booealn'),
        	'parent_task_id' => array('nullableExists:tasks,id'),
        	'task_id' => array('nullableExists:tasks,id'),
        	'team' => array('required', 'between:1,64', 'string'),
        	'user_rank' => array('required', 'numeric', 'between:'. Auth::user()->user_rank .',9'),
        	);
        	
        if (isset($inputs['user_id'])) {
			foreach ($inputs['user_id'] as $index => $user_id) {
	        	$ruls[] = array('user_id.'.$index => array('exists:users,id'));
			}
		}
		
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$result = DB::transaction(function() {
			        $inputs = Input::all();
			        $inputs['priority'] = intval($inputs['priority']);
			        $inputs['time_span'] = floatval($inputs['time_span']);
					// バリデーションはうえで済ませてある
	
					// 更新
			        if ( isset($inputs['user_id']) && $inputs['task_id'] > 0 ) {
						$msg = "更新";
						$task = Task::where('id', $inputs['task_id'])->first();
						$task->fill($inputs)->save();
					}
					// 新規作成
					else {
						$msg = "登録";
				        $task = Task::create( array(
				        	'done' => isset($inputs['done']),
				        	'parent_task_id' => $inputs['parent_task_id'],
				        	'title' => $inputs['title'],
				        	'deadline' => $inputs['deadline'],
				        	'priority' => $inputs['priority'],
				        	'note' => $inputs['note'],
				        	'time_span' => $inputs['time_span'],
				        	'user_rank' => $inputs['user_rank'],
				        	) );
						Log::debug($task);
					}
		
					/**
					 * タスクに関連付けられているユーザリストの登録または更新
					 */
					// 既存のタスクに関連付けられているユーザを全削除
					TaskStaff::where('task_id', $task->id)->delete();
		
					// 既存のタスクに入力されたユーザリストを追加
					$user_ids = [];
			        if (isset($inputs['user_id'])) {
						foreach ($inputs['user_id'] as $index => $user_id) {
					        $elem = array(
								'task_id' => $task->id,
								'user_id' => intval($user_id),
								);
							Log::debug( $elem );
							$task = TaskStaff::create($elem );
							$user_ids[] = $task['user_id'];
						}
					}
					
					return array(
						'msg' => $msg, 
						'user_ids' => $user_ids
						);
				});
			} catch (Exception $e) {
			    return Redirect::back()->with('msg', "タスク $inputs[title] の保存に失敗しました。");
			}

			Session::set('msg', "タスク $inputs[title] を$result[msg]しました。");
            return View::make( 'task_edit', [
                'task_id' => null, 
                'user' => Auth::user(), 
                'team_info' => array('staff' => User::where('team', Auth::user()->team)->get(), 'staff_id' => $result['user_ids']),
                'record' => $result['task'],
                ]);
		}
	}


	public function delete()
	{
        $inputs = Input::only('delete', 'task_id');
        Log::debug($inputs);

        $rules = array(
        	'delete' => array('booealn'),
        	'task_id' => array('nullable', 'exists:tasks,id'),
        	);
        	
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			if ($inputs['task_id'] != null) {
				// 上位の権限を持つタスクは削除させない
				if (Task::where('id', $inputs['task_id'])->pluck('user_rank') < Auth::user()->user_rank) {
			        return Redirect::back()->withErrors(['msg' => "タスク ${inputs['title']} を削除する権限がありません。"]);
				}
				else {
					DB::transaction(function () {
						Task::where('id', $inputs['task_id'])->delete();
		
						// 既存のタスクに関連付けられているユーザを全削除
						TaskStaff::where('task_id', $inputs['task_id'])->delete();
					});
				}

			    return Redirect::back()->with('msg', "タスク ${inputs['title']} を削除しました。");
			}
			else {
			    return Redirect::back()->with('msg', "削除するタスクがありません。");
			}
			
		}
	}

	public function validationNullableExists($attribute, $value, $parameters)
	{
		Log::debug($attribute);
		Log::debug($value);
		Log::debug($parameters);
		
	    if (isset($value) && $value == null) return true;
	
		foreach ($parameters as $param ) {
			if ( !isset($param) ||
				DB::table($parameters[0])->where($parameters[1], $value)->count() <= 0 ) {
	
				return false;
			}
		}
		return true;
	}
}
