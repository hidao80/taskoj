<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends BaseController {

	public function get()
	{
		Log::debug('enter: '.get_class($this).' > '.__FUNCTION__);

        $inputs = Input::only('task_id');
    	$inputs['task_id'] = intval($inputs['task_id']);

        $rules = array(
        	'task_id' => array('nullableExists:tasks,id'),
			);
			
		Validator::extend('nullableExists', 'TaskController@validationNullableExists');
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
			Session::set('msg', "タスクID ${inputs['task_id']} は存在しません。");
        	return View::make( 'task/edit', [
                'task_id' => null, 
                'user_rank' => Auth::user()->user_rank, 
                'team' => Auth::user()->team,
                'team_info' => array(
                    'user_list' => User::getUserList(), 
                    'staff_list' => [],
                    ),
                'record' => null,
                ]);
		}
		else {
			$task = Task::find($inputs['task_id']);
			
			if ( count($task) <= 0 ) {
				$task = null;
			}
			
			Log::debug($task);
            return View::make( 'task/edit', [
                'task_id' => $inputs['task_id'], 
                'user_rank' => Auth::user()->user_rank, 
                'team' => Auth::user()->team,
                'team_info' => array(
                	'user_list' => User::getUserList(), 
                	'staff_list' => $result['staff_list'],
                	),
                'record' => $task,
                ]);
		}
	}

	public function getId($id)
	{
		Log::debug('enter: '.get_class($this).' > '.__FUNCTION__);

    	$inputs['task_id'] = intval($id, 10);

        $rules = array(
        	'task_id' => array('exists:tasks,id'),
			);
			
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
			Session::set('msg', "タスクID ${inputs['task_id']} は存在しません。");
        	return View::make( 'task/edit', [
                'task_id' => $inputs['task_id'], 
                'team' => Auth::user()->team, 
                'user_rank' => Auth::user()->user_rank, 
                'team_info' => array(
                	'user_list' => User::getUserList(), 
                	'staff_list' => [],
                	),
                'record' => null,
                ]);
		}
		else {
			$task = Task::find($inputs['task_id']);
			
			if ( count($task) <= 0 ) {
				$task = null;
			}
			
			$staff_list = TaskStaff::where('task_id', $inputs['task_id'])->lists('user_id');

			Log::debug($task);
			Session::set('msg', '');
            return View::make( 'task/edit', [
                'task_id' => $inputs['task_id'], 
                'team' => Auth::user()->team, 
                'user_rank' => Auth::user()->user_rank, 
                'team_info' => array(
                	'user_list' => User::getUserList(), 
                	'staff_list' => $staff_list,
                	),
                'record' => $task->toArray(),
                ]);
		}
	}

	public function edit()
	{
		Log::debug('enter: '.get_class($this).' > '.__FUNCTION__);

        $inputs = Input::all();
        $inputs['priority'] = intval($inputs['priority']);
        $inputs['time_span'] = floatval($inputs['time_span']);
        $inputs['user_rank'] = intval($inputs['user_rank']);
        $inputs['task_id'] = isset($inputs['task_id']) ? intval($inputs['task_id']) : null;
        $inputs['parent_task_id'] = $inputs['parent_task_id'] != '' ? intval($inputs['parent_task_id']) : null;
        Log::debug($inputs);

		Validator::extend('nullableExists', 'TaskController@validationNullableExists');

        $rules = array(
        	'done' => array('boolean'),
        	'title' => array('required', 'between:1,255', 'string'),
        	'deadline' => array('date'),
        	'priority' => array('required', 'integer', 'min:1'),
        	'note' => array('string'),
        	'time_span' => array('numeric', 'min:0'),
        	'delete' => array('boolean'),
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
			Log::debug($val);
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			try {
				$result = DB::transaction(function() use ( $inputs ) {
					Log::debug('enter: '.__FUNCTION__);
					// 更新
			        if ( $inputs['task_id'] != null ) {
						Log::debug('enter: 更新 ');
						$msg = "更新";
						Log::debug($inputs['task_id']);
						$task = Task::find($inputs['task_id']);
						$task->fill( array(
				        	'done' => isset($inputs['done']),
				        	'parent_task_id' => $inputs['parent_task_id'],
				        	'title' => $inputs['title'],
				        	'deadline' => $inputs['deadline'],
				        	'priority' => $inputs['priority'],
				        	'note' => $inputs['note'],
				        	'time_span' => $inputs['time_span'],
				        	'user_rank' => $inputs['user_rank'],
							) )->save();
						Log::debug('filled.: ');
					}
					// 新規作成
					else {
						Log::debug('enter: 登録');
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
					// タスクに関連付けられているユーザを全削除
					Log::debug('TaskStaff->delete():');
					TaskStaff::where('task_id', $task->id)->delete();
		
					// タスクに入力された担当者リストを追加
					foreach ( $inputs['user_id'] as $user_id ) {
						TaskStaff::create( array(
							'task_id' => $task->id,
							'user_id' => intval($user_id),
							) );
					}
					
					return array(
						'msg' => $msg, 
						'staff_list' => $inputs['user_id'],
						'task' => $task->toArray(),
						);
				});
			} catch (Exception $e) {
				Log::debug("タスク $inputs[title] の保存に失敗しました。". $e);
			    return Redirect::back()->withInput()->with('msg', "タスク $inputs[title] の保存に失敗しました。");
			}

			Log::debug("タスク $inputs[title] を$result[msg]しました。");
			Session::set('msg', "タスク $inputs[title] を$result[msg]しました。");
            return View::make( 'task/edit', [
                'task_id' => null, 
                'team' => Auth::user()->team, 
                'user_rank' => Auth::user()->user_rank, 
                'team_info' => array(
                	'user_list' => User::getUserList(), 
                	'staff_list' => $result['staff_list'],
                	),
                'record' => $result['task'],
                ]);
		}
	}


	public function delete($id)
	{
		Log::debug('enter: '.get_class($this).' > '.__FUNCTION__);

    	$inputs['task_id'] = intval($id, 10);

        $rules = array(
        	'task_id' => array('exists:tasks,id'),
			);
			
		$val = Validator::make($inputs, $rules);

		if ( $val->fails() ) {
	        return Redirect::back()->withErrors($val)->withInput();
		}
		else {
			$task = Task::find($inputs['task_id']);
			if ($task != null) {
				$taskTitle = $task->title;

				// 上位の権限を持つタスクは削除させない
				if ($task->user_rank < Auth::user()->user_rank) {
					Log::debug("task->user_rank: ". $task->user_rank);
					Log::debug("Auth::user()->user_rank: ". Auth::user()->user_rank);
					Log::debug('return: '.trans('taskoj.not_have_permission', ['task_title' => $taskTitle]));
			        return Redirect::to('task/list')->with('msg', trans('taskoj.not_have_permission', ['task_title' => $taskTitle]));
				}
				else {
					DB::transaction(function( $inputs ) use ( $inputs ) {
						Task::find($inputs['task_id'])->delete();
		
						// 既存のタスクに関連付けられているユーザを全削除
						TaskStaff::where('task_id', $inputs['task_id'])->delete();
					});
				}

				Log::debug('return: '.trans('taskoj.has_been_deleted', ['task_title' => $taskTitle]));
			    return Redirect::to('task/edit')->with('msg', trans('taskoj.has_been_deleted', ['task_title' => $taskTitle]));
			}
			else {
				Log::debug('return: '.trans('taskoj.no_tasks_to_delete'));
			    return Redirect::back()->with('msg', trans('taskoj.no_tasks_to_delete'));
			}
		}
	}

	public function validationNullableExists($attribute, $value, $parameters)
	{
		//Log::debug($attribute);
		//Log::debug($value);
		//Log::debug($parameters);
		
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
