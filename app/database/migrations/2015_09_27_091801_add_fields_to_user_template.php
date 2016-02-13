<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUserTemplate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	Schema::table('user_templates', function ( $table ) {
            $table->integer('integration_id')->nullable();
            $table->string('contact_type')->nullable();            
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		       Schema::table( 'user_templates', function ( $table ) {
            $table->dropColumn( 'integration_id' );
            $table->dropColumn( 'contact_type' );
        } );
	}

}
