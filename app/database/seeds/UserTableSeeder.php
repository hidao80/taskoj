<?php
 
class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table( 'users' )->delete();
 
        User::create( array(
            'username' => 'test',
            'password' => 'test',
            'team' => 'test',
            'remember_token' => "",
        ) );
    }
 
}