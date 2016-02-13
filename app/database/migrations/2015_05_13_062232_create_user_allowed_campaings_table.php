<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAllowedCampaingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'user_allowed_campaigns', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'campaign_id' );
            $table->integer( 'user_id' );
            $table->timestamps();
            $table->index( 'campaign_id' );
            $table->index( 'user_id' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop( 'user_allowed_campaigns' );
    }

}
