<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

define('ADMIN_USER_ID', 1);

class User extends Eloquent implements UserInterface, RemindableInterface {

    
	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
	 * createするとき、以下の列は引数を必要としない。
	 *
	 * @var array
	 */
    protected $guarded = array('id', "updated_at", "created_at");

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    
    public static function getUserList() {
        $displayName = [];
        $users = User::where('team', Auth::user()->team)->select('display_name', 'id')->get()->toArray();
        Log::debug('users: '. var_export($users, true));

        return $users;
    }
}
