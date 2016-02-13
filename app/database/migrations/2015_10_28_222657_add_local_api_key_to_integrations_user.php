<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalApiKeyToIntegrationsUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('integrations_user', function(Blueprint $table)
		{
			$table->string('local_api_key')->after('organizerKey')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('integrations_user', function(Blueprint $table)
		{
			$table->dropColumn('local_api_key');
		});
	}

}
