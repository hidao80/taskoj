@if ( $errors->any() )
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
@if ( Auth::guest() )
    未ログイン
@else
    ログイン済みです
@endif

{{ Form::open(['action' => 'AuthController@login']) }}
{{ Form::label('user_name', 'ユーザー名：') }}
{{ Form::text('user_name', Input::old('user_name', '')) }}
<br>
{{ Form::label('password', 'パスワード：') }}
{{ Form::password('password') }}
<br>
{{ Form::label('team', 'チーム名：') }}
{{ Form::text('team', Input::old('team', '')) }}
<br>
{{ Form::submit('ログイン'); }}
{{ Form::close() }}