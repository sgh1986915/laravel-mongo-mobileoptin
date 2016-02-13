<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSerculMessagesReadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            if (!Schema::hasTable('messages_read')) {
		Schema::create('messages_read', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer( 'message_id' );
                        $table->integer( 'user_id' )->nullable();
                        $table->string( 'status' )->default(0)->nullable();
                        $table->index( 'message_id' );
                        $table->index( 'user_id' );
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
		Schema::drop('messages_read');
	}

}
