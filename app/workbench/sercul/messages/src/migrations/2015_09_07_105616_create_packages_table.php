<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->integer('status')->nullable();
                $table->string('version')->nullable();
                $table->timestamp('added_date')->nullable();
            });
        }
        DB::table('modules')->insert([
            'name' => 'Messages',
            'version' => '0.1',
            'added_date' => time(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('modules');
    }

}
