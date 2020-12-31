@if ( $errors->any() )
        {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
{{{ Session::has('msg') ? Session::get('msg') : '' }}}

@if ( $type === 'add' )
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
@elseif ( $type === 'update' )
    {{ Form::open(['action' => 'UserController@update']) }}
    {{ Form::label('user_name', 'ユーザー名：') }}
    {{ Form::text('user_name', Input::old('user_name', '')) }}
    <br>
    {{ Form::label('display_name', 'ユーザー表示名：') }}
    {{ Form::text('display_name', '') }}
    <br>
    {{ Form::label('password', 'パスワード：') }}
    {{ Form::password('password') }}
    <br>
    {{ Form::label('team', 'チーム名：') }}
    {{ Form::text('team', Input::old('team', '')) }}
    <br>
    {{ Form::label('user_rank', '権限レベル：') }}
    {{ Form::text('user_rank', Input::old('user_rank', '')) }}
@elseif ( $type === 'delete' )
    {{ Form::open(['action' => 'UserController@delete']) }}
    {{ Form::label('user_name', 'ユーザー名：') }}
    {{ Form::text('user_name', '') }}
    <br>
    {{ Form::label('team', 'チーム名：') }}
    {{ Form::text('team', Input::old('team', '')) }}
    <br>
@endif
<br>
{{ Form::submit('送信'); }}
{{ Form::close() }}