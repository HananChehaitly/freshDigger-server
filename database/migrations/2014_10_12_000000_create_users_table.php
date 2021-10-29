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
            $table->integer('user_type_id');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('user_type_id');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('bio');        
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
            $table->integer('amount')->default('0');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
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
			$table->date('day');
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
