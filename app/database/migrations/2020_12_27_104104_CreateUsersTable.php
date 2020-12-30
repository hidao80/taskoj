<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists("users");
        Schema::create( 'users', function($table)
            {
                $table->increments( 'id' );
                $table->string( 'username', 20 );
                $table->string( 'password', 64 );
                $table->string( 'team', 64 );
                $table->string( 'remember_token' );
                $table->tinyInteger( 'user_rank' );  // 権限max=0(admin), 最弱min=9(guest):
                $table->timestamps();
            } );
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'users' );
    }
 
}
