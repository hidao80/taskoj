<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListController extends BaseController {

	public function get()
	{
		Log::debug('enter: '.get_class($this).' > '.__FUNCTION__);

		// 指定されたログインユーザのもつタスクIDの配列を取得する
		// 管理者でログインしているときはすべてのタスクが見える
		if (Auth::user()->id == ADMIN_USER_ID) {
			$tasks = TaskStaff::leftJoin('tasks', 'tasks.id', '=', 'task_staff.task_id')
				->select('user_id', 'done', 'parent_task_id', 'title', 'deadline', 'priority', 'time_span', 'note', 'task_id', 'tasks.user_rank as user_rank')
				->get()->toArray();
		}
		else {
			$tasks = TaskStaff::where('user_id', '=', Auth::user()->id)
				->leftJoin('tasks', 'tasks.id', '=', 'task_staff.task_id')
				->select('user_id', 'done', 'parent_task_id', 'title', 'deadline', 'priority', 'time_span', 'note', 'task_id', 'tasks.user_rank as user_rank')
				->get()->toArray();
		}
		Log::debug('Auth::user()->id: '. var_export(Auth::user()->id, true));
		Log::debug('Auth::user()->team: '. var_export(Auth::user()->team, true));
		Log::debug('tasks: '. var_export($tasks, true));


		$taskNodes = [];
		if ( count($tasks) > 0 ) {
			// タスクのリストをツリー構造に作り替え
			$taskNodes = [];
			$userInTeam = User::getUserList();
			
			foreach ( $tasks as $task ) {
				$currentTask = [];

				$users = TaskStaff::where('task_id', '=', $task['task_id'])
					->leftJoin('users', 'users.id', '=', 'task_staff.user_id')
					->select('task_id', 'user_id', 'display_name', 'team');

				$currentTask['task'] = $task;
				$currentTask['teamInfo'] = array(
					'user_list' => $userInTeam,
					'staff_list' => $users->where('task_id', $task['task_id'])->lists('user_id'),
					'team' => $users->where('user_id', $task['user_id'])->pluck('team'),
					);
				$currentTask['children'] = [];
				$currentTask['no_parent'] = true;
				
				$taskNodes[$currentTask['task']['task_id']] = $currentTask;
			}
			//Log::debug('$taskNodes(0): '. var_export($taskNodes, true));

			// 親ノードがある場合は親子関係をつけて、リストアップした配列から削除する
			foreach ( $taskNodes as $parentIndex => $parenttTask ) {
				foreach ( $taskNodes as $childIndex => $childTask ) {
					Log::debug('p: '.$parenttTask['task']['task_id'].', c: '.$childTask['task']['task_id'] );
					Log::debug('parent_id: '.$childTask['task']['parent_task_id'] );
					if ( $parenttTask['task']['task_id'] == $childTask['task']['task_id'] ) {
						continue;
					}
					else if ( $parenttTask['task']['task_id'] == $childTask['task']['parent_task_id'] ) {
						Log::debug('paired! ' );
						
						// ループ変数は実態でないので $taskNodes の要素に代入する
						// ただのコピーだと親子関係が確定後に子供がつかないので、
						// 子供は参照私とする
						$taskNodes[$parentIndex]['children'][] = &$taskNodes[$childIndex];
						
						// 子側に親がいるフラグを立てる
						$taskNodes[$childIndex]['no_parent'] = false;
					}
				}
			}
		}
		
		Log::debug('$taskNodes(return): '. var_export($taskNodes, true));
        return View::make( 'task/list', [
            'taskNodes' => $taskNodes,
            ]);
	}
}
