@if ( Auth::guest() )
    未ログイン
@else
    ログイン済みです
@endif

{{ Form::open(['action' => 'AuthController@login']) }}
{{ Form::label('username', 'ユーザー名：') }}
{{ Form::text('username', Input::old('username', '')) }}
<br>
{{ Form::label('password', 'パスワード：') }}
{{ Form::password('password') }}
<br>
{{ Form::label('team', 'チーム名：') }}
{{ Form::text('team', Input::old('team', '')) }}
<br>
{{ Form::submit('ログイン'); }}
{{ Form::close() }}