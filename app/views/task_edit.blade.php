@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

{{ Form::open(['action' => 'TaskController@edit']) }}
{{ Form::checkbox('done', Input::old('done', $record != null ? $record['done'] : '')) }}
<br>
{{ Form::label('parent_task_id', '親タスクID：') }}
{{ Form::number('parent_task_id',  Input::old('parent_task_id', $record != null ? $record['parent_task_id'] : '')) }}
<br>
{{ Form::label('title', '題名：') }}
{{ Form::text('title', Input::old('title', $record != null ? $record['title'] : '')) }}
<br>
{{ Form::label('deadline', '期日：') }}
<input type='date' name='deadline' value="{{ Input::old('deadline', $record != null ? $record['deadline'] : '') }}">
<br>
{{ Form::label('priority', '優先度：') }}
{{ Form::selectRange('priority', 1, 10, Input::old('priority', $record != null ? $record['priority'] : '')) }}
<br>
担当者：<br>
@foreach ( $team_info['staff'] as $staff )
@foreach ( $team_info['staff_id'] as $staff_id )
{{ Form::checkbox('user_id[]', $staff->id, $staff->id == $staff_id ? true : false) }}
@endforeach
{{ Form::label('user_id[]', $staff->display_name) }}
@endforeach
<br>
{{ Form::label('note', 'メモ：') }}
{{ Form::textarea('note', Input::old('note', $record != null ? $record['note'] : '')) }}
<br>
{{ Form::label('time_span', '所要時間：') }}
{{ Form::text('time_span', Input::old('time_span', $record != null ? $record['time_span'] : '')) }}
<br>
{{ Form::checkbox('delete', 'yes', false) }}
{{ Form::label('delete', '削除') }}
<br>
{{ Form::hidden('task_id', $task_id) }}
{{ Form::hidden('team', $user->team) }}
{{ Form::hidden('user_rank', $user->user_rank) }}
{{ Form::submit('送信'); }}
{{ Form::close() }}