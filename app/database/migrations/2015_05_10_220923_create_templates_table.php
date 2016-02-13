<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'campaigns_templates', function ( $table ) {
            $table->increments( 'id' );
            $table->string( 'name' );
            $table->string( 'thumb' );
            $table->string( 'path' );
            $table->boolean( 'active' );
            $table->timestamp( 'activated_on' );
            $table->timestamp( 'deactivated_on' );
            $table->timestamps();
            $table->softDeletes();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'campaigns_templates' );
    }

}
