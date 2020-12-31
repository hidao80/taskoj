<?php
 
class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table( 'users' )->delete();
 
        User::create( array(
            'user_name' => 'admin',
            'display_name' => 'システム管理者',
            'password' => 'adminadmin',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 0,
        ) );

        User::create( array(
            'user_name' => 'user01',
            'display_name' => 'サンプル担当者1',
            'password' => 'user01',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 8,
        ) );

        User::create( array(
            'user_name' => 'user02',
            'display_name' => 'サンプル担当者2',
            'password' => 'user02',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 8,
        ) );

        User::create( array(
            'user_name' => 'guest',
            'display_name' => 'ゲストユーザ',
            'password' => 'guest',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 9,
        ) );
    }
 
}