<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('package_user')) {
            Schema::create('package_user', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('package_id');
                $table->integer('user_id');
                $table->string('status')->default(0)->nullable();
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
        Schema::drop('package_user');
    }

}
