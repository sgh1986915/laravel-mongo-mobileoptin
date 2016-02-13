<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('package', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('name');
                        $table->integer('max_campaigns');
                        $table->smallInteger('split_testing')->nullable();
                        $table->smallInteger('redirect_page')->nullable();
                        $table->smallInteger('embed')->nullable();
                        $table->smallInteger('hosted')->nullable();
                        $table->smallInteger('analytics_retargeting')->nullable();
                        $table->smallInteger('status');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('package');
	}

}
