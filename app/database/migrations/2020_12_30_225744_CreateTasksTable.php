<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists("tasks");
        Schema::create( 'tasks', function($table)
            {
                $table->increments( 'id' );
                $table->integer( 'parent_task_id' )->nullable();
                $table->boolean( 'done' );
                $table->string( 'title', 255 );
                $table->date( 'deadline' )->nullable();
                $table->tinyInteger( 'priority' )->unsigned();  // 最優先x=0, 数字が大きいほど優先度が低い
                $table->string( 'note' );
                $table->float( 'time_span' )->unsigned();
                $table->tinyInteger( 'user_rank' )->unsigned();  // 権限max=0(admin), 最弱min=9(guest):
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
        Schema::dropIfExists( 'tasks' );
	}

}
