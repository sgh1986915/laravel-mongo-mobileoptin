<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCampaignsAddSlug extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'campaigns', function ( $table ) {
            $table->string( 'slug' );


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
            $table->dropColumn( 'slug' );

        } );
    }

}
