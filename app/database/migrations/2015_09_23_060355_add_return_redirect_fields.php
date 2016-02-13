<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnRedirectFields extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('campaigns', function ( $table ) {
            $table->integer('enable_return_redirect')->nullable();
            $table->integer('redirect_return_after')->nullable();
            $table->string('redirect_return_url')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
       Schema::table( 'campaigns', function ( $table ) {
            $table->dropColumn( 'enable_return_redirect' );
            $table->dropColumn( 'redirect_return_after' );
            $table->dropColumn( 'redirect_return_url' );
        } );
    }

}
