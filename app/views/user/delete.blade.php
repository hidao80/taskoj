@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

{{ Form::open(['action' => 'UserController@delete']) }}
{{ Form::label('user_name', 'ユーザー名：') }}
{{ Form::text('user_name', '') }}
<br>
{{ Form::label('team', 'チーム名：') }}
{{ Form::text('team', Input::old('team', '')) }}
<br>
{{ Form::submit('送信'); }}
{{ Form::close() }}