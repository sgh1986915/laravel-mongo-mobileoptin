<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignTableAddAutoOptimisation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table( 'campaigns', function ( $table ) {
            $table->integer( 'ao_clicks' );
            $table->integer( 'ao_threshold' );



        } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table( 'campaigns', function ( $table ) {
            $table->dropColumn( 'ao_clicks' );
            $table->dropColumn( 'ao_threshold' );


        } );
	}

}
