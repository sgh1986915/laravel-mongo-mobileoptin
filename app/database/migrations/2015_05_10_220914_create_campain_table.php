<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampainTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'campaigns', function ( $table ) {
            $table->increments( 'id' );
            $table->integer( 'user_id' );
            $table->string( 'name' );
            $table->integer( 'template_id' );
            $table->string( 'notification_email' );
            $table->string( 'redirect_to' );
            $table->boolean( 'active' );
            $table->timestamp( 'activated_on' );
            $table->timestamp( 'deactivated_on' );
            $table->timestamps();
            $table->softDeletes();
            $table->index( 'user_id' );
            $table->index( 'template_id' );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'campaigns' );
    }

}
