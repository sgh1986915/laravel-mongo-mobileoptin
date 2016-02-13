<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'campaigns', function ( $table ) {
            $table->text( 'email_body' );
            $table->string( 'email_subject' );

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
            $table->dropColumn( 'email_body' );
            $table->dropColumn( 'email_subject' );
        } );
    }

}
