<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTemplateTb extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table( 'user_templates', function ( $table ) {
            $table->text( 'email_message' );
            $table->string( 'notification_email' );
            $table->string( 'email_subject' );
            $table->string( 'redirect_after' );


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
            $table->dropColumn( 'redirect_after' );
            $table->dropColumn( 'email_subject' );
            $table->dropColumn( 'notification_email' );
            $table->dropColumn( 'email_message' );

        } );
    }

}
