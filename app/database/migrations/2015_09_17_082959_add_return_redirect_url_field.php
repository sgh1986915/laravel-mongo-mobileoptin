<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnRedirectUrlField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	Schema::table( 'user_templates', function ( $table ) {
            $table->string('return_redirect')->nullable();
        } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
					        Schema::table( 'user_templates', function ( $table ) {
            $table->dropColumn( 'return_redirect' );
        } );
	}

}
