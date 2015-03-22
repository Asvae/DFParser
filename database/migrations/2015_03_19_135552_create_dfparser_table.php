<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDfparserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dfparser', function(Blueprint $table)
		{
            $table->increments('id');
            $table->timestamps();
            $table->text('file');
            $table->text('item');
            $table->json('json');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dfparser');
	}

}
