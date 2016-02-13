<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationsUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('integrations_user')) {
            Schema::create('integrations_user', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('integration_id');
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('api_key')->nullable();
                $table->index('package_id');
                $table->index('user_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('integrations_user');
    }


}
