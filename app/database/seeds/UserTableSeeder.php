<?php
 
class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table( 'users' )->delete();
 
        User::create( array(
            'username' => 'admin',
            'password' => 'adminadmin',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 0,
        ) );

        User::create( array(
            'username' => 'test',
            'password' => 'test',
            'team' => 'default',
            'remember_token' => "",
            'user_rank' => 8,
        ) );
    }
 
}