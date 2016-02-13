<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTemplateTableAddFooterLinks extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'user_templates', function ( $table ) {
            $table->string( 'terms' );
            $table->string( 'privacy' );
            $table->string( 'contact_us' );

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
            $table->dropColumn( 'contact_us' );
            $table->dropColumn( 'privacy' );
            $table->dropColumn( 'terms' );
        } );
    }

}
