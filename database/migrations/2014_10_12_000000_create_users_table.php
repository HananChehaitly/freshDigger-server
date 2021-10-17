<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->timestamp('username_verified_at')->nullable();
            $table->string('password');
            $table->integer('category_id');
            $table->integer('weekly_limit');
            $table->text('picture_url');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id');
            $table->integer('business_id');	
            $table->integer('amount');
            $table->timestamps();
			$table->softDeletes();
        });
		
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();
			$table->integer('business_id');
            $table->point('longitude'); 	
            $table->point('latitude');	
            $table->timestamps();
			$table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
			$table->string('name');	
            $table->timestamps();
			$table->softDeletes();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->id();
			$table->date('date');
            $table->float('rate'); 		
            $table->timestamps();
			$table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
