<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOwnerTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'user_owner', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'owner_id' );
            $table->integer( 'user_id' );
            $table->timestamps();
            $table->index( 'owner_id' );
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
        Schema::drop( 'user_owner' );
    }

}
