<?php
 
class TaskTableSeeder extends Seeder
{
    public function run()
    {
        DB::table( 'tasks' )->delete();
 
        $parent = Task::create( array(
            'done' => false,
            'title' => 'タスクA',
            'deadline' => '2021-01-01',
            'priority' => 3,
            'note' => 'タスクAの内容とやったこと',
            'time_span' => 3.5,
            'parent_task_id' => null,
            'user_rank' => 0,
        ) );
 
        Task::create( array(
            'done' => false,
            'title' => 'タスクB',
            'deadline' => '2020-12-31',
            'priority' => 3,
            'note' => 'タスクBの内容とやったこと',
            'time_span' => 1,
            'parent_task_id' => $parent->id,
            'user_rank' => 8,
        ) );
 
        Task::create( array(
            'done' => false,
            'title' => 'タスクC',
            'deadline' => '2021-01-15',
            'priority' => 1,
            'note' => 'タスクCの内容とやったこと',
            'time_span' => 2.25,
            'parent_task_id' => null,
            'user_rank' => 8,
        ) );

        /**
         *  task_staffテーブルも同時に作成
         */
        DB::table( 'task_staff' )->delete();
 
        TaskStaff::create( array(
            'task_id' => DB::table('tasks')->where('title', 'タスクA')->pluck('id'),
            'user_id' => DB::table('users')->where('user_name', 'user01')->where('team', 'default')->pluck('id'),
        ) );
 
        TaskStaff::create( array(
            'task_id' => DB::table('tasks')->where('title', 'タスクA')->pluck('id'),
            'user_id' => DB::table('users')->where('user_name', 'user02')->where('team', 'default')->pluck('id'),
       ) );
 
        TaskStaff::create( array(
            'task_id' => DB::table('tasks')->where('title', 'タスクB')->pluck('id'),
            'user_id' => DB::table('users')->where('user_name', 'user01')->where('team', 'default')->pluck('id'),
        ) );
 
   }
 
}