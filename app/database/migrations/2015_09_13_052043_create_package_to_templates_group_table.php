<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageToTemplatesGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('package_to_templates_group', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('package_id');
                        $table->integer('template_group_id');
                        $table->index('package_id');
                        $table->index('template_group_id');
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
		Schema::drop('package_to_templates_group');
	}

}
