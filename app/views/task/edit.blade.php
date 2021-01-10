@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{ Session::get('msg') }}

{{ Form::open(['action' => 'TaskController@edit']) }}
{{ Form::label('task_id', trans('taskoj.task_id').'：'. $task_id) }}
<br>
{{ Form::checkbox('done', Input::old('true', $record != null ? true : null)) }}
<br>
{{ Form::label('parent_task_id', trans('taskoj.parent_id').'：') }}
{{ Form::number('parent_task_id',  Input::old('parent_task_id', $record != null ? $record['parent_task_id'] : '')) }}
<br>
{{ Form::label('title', trans('taskoj.title').'：') }}
{{ Form::text('title', Input::old('title', $record != null ? $record['title'] : '')) }}
<br>
{{ Form::label('deadline', trans('taskoj.deadline').'：') }}
<input type='date' name='deadline' value="{{ Input::old('deadline', $record != null ? $record['deadline'] : '') }}">
<br>
{{ Form::label('priority', trans('taskoj.priority').'：') }}
{{ Form::selectRange('priority', 1, 10, Input::old('priority', $record != null ? $record['priority'] : '')) }}
<br>
@lang('taskoj.staff')：<br>
@foreach ( $team_info['user_list'] as $user)
{{ Form::checkbox('user_id[]', $user['id'], in_array($user['id'], $team_info['staff_list'] ) ) }}
{{ Form::label('user_id[]', $user['display_name']) }}
@endforeach
<br>
{{ Form::label('note', trans('taskoj.note').'：') }}
{{ Form::textarea('note', Input::old('note', $record != null ? $record['note'] : '')) }}
<br>
{{ Form::label('time_span', trans('taskoj.time_span').'：') }}
{{ Form::text('time_span', Input::old('time_span', $record != null ? $record['time_span'] : '')) }}
<br>
{{ Form::hidden('task_id', $task_id) }}
{{ Form::hidden('team', $team) }}
{{ Form::hidden('user_rank', $user_rank) }}
{{ Form::submit('送信'); }}
{{ Form::close() }}
<button onclick="confirmDialog('{{ URL::to('task/delete/'. $task_id) }}', '@lang("taskoj.delete_it?")')">@lang('taskoj.delete')</button>

<script src="{{ asset('js/dialog.js') }}"></script>