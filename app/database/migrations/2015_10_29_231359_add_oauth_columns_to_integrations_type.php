<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOauthColumnsToIntegrationsType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('integrations_type', function(Blueprint $table)
		{
			$table->string('oauth_key');
			$table->string('oauth_secret');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('integrations_type', function(Blueprint $table)
		{
			$table->dropColumn('oauth_key');
            $table->dropColumn('oauth_secret');
		});
	}

}
