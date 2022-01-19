<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTables extends Migration
{
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('email');
			$table->foreignId('company_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
		
		Schema::create('companies', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->timestamps();
			$table->softDeletes();
		});
	}
	
	public function down()
	{
		Schema::dropIfExists('companies');
		Schema::dropIfExists('users');
	}
}
