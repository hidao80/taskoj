@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

{{ Form::open(['action' => 'TaskEditController@edit']) }}
{{ Form::checkbox('done', true, [], ['class' => ''])) }}
<br>
{{ Form::label('title', '題名：') }}
{{ Form::text('title', Input::old('title', '')) }}
<br>
{{ Form::label('deadline', '期日：') }}
{{ Form::text('deadline', Input::old('deadline', '')) }}
<br>
{{ Form::label('priority', '優先度：') }}
{{ Form::text('priority', Input::old('priority', '')) }}
<div>
担当者
@foreach ($users as $user)
{{ Form::checkbox($user->username, true, [], ['class' => '']) }}
{{ Form::label('priority', $user->username) }}
@endforeach
</div>
{{ Form::label('note', 'メモ：') }}
{{ Form::textarea('note', Input::old('note', '')) }}
<br>
{{ Form::label('time_span', '所要時間：') }}
{{ Form::text('time_span', Input::old('time_span', '')) }}
<br>
{{ Form::submit('送信'); }}
{{ Form::close() }}