<?php

/**
 * @file classes/migration/PPNSchemaMigration.inc.php
 *
 * @class PPNSchemaMigration
 * @brief Describe database table structures.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class PPNSchemaMigration extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{

		// PPNs.
		Capsule::schema()->create('ppns', function (Blueprint $table) {
			$table->bigInteger('ppn_id')->autoIncrement();
			$table->bigInteger('submission_id');
			$table->bigInteger('context_id');
		});

		// PPN Settings.
		Capsule::schema()->create('ppn_settings', function (Blueprint $table) {
			$table->bigInteger('ppn_id');
			$table->string('locale', 14)->default('');
			$table->string('setting_name', 255);
			$table->longText('setting_value')->nullable();
			$table->string('setting_type', 6)->comment('string');
			$table->index(['ppn_id'], 'ppn_settings_id');
			$table->unique(['ppn_id', 'setting_name'], 'ppn_settings_pkey');
		});
	}
}