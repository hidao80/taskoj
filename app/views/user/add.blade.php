@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

{{ Form::open(['action' => 'UserController@create']) }}
{{ Form::label('user_name', 'ユーザー名：') }}
{{ Form::text('user_name', '') }}
<br>
{{ Form::label('display_name', 'ユーザー表示名：') }}
{{ Form::text('display_name', '') }}
<br>
{{ Form::label('password', 'パスワード：') }}
{{ Form::password('password') }}
<br>
{{ Form::label('team', 'チーム名：') }}
{{ Form::text('team', '') }}
<br>
{{ Form::label('user_rank', '権限レベル：') }}
{{ Form::text('user_rank', Input::old('user_rank', '')) }}
<br>
{{ Form::submit('送信'); }}
{{ Form::close() }}