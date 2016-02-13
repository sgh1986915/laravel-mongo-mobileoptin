<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSerculMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            if (!Schema::hasTable('messages')) {
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string( 'topic' )->nullable();
                        $table->text( 'content' )->nullable();
                        $table->string( 'status' )->nullable();
                        $table->timestamps();
                        $table->softDeletes();
            });}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
