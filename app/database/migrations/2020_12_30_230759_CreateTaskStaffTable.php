<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskStaffTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists("task_staff");
        Schema::create( 'task_staff', function($table)
            {
                $table->increments( 'id' );
                $table->integer( 'task_id' )->unsigned();
                $table->integer( 'user_id' )->unsigned();
                $table->timestamps();
                $table->foreign('task_id')->references('id')->on('tasks');
				$table->foreign('user_id')->references('id')->on('users');
            } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists( 'task_staff' );
	}

}
