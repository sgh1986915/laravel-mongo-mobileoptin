<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTemplateAddSplitTestingPercentage extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table( 'user_templates', function ( $table ) {
            $table->integer( 'affect_percentile' );


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
            $table->dropColumn( 'affect_percentile' );

        } );
    }

}
