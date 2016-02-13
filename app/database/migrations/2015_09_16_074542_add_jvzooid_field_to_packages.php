<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJvzooidFieldToPackages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		        Schema::table( 'package', function ( $table ) {
            $table->integer( 'jvzoo_id' );

        } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			        Schema::table( 'package', function ( $table ) {
            $table->dropColumn( 'jvzoo_id' );
        } );
	}

}
