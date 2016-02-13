<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableCampaignTemplatesAddGroup extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'campaigns_templates', function ( $table ) {
            $table->integer( 'group_id' );

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'campaigns_templates', function ( $table ) {
            $table->dropColumn( 'group_id' );
        } );
    }

}
