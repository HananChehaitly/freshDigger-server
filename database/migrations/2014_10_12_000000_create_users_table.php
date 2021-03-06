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
            $table->string('expoToken');
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('bio');        
            $table->integer('category_id');
            $table->integer('weekly_limit');
            $table->string('latitude');        
            $table->string('longitude');   
            $table->string('Area');   
            $table->text('picture_url');
            $table->string('phone_number');  
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id');
            $table->integer('business_id');	
            $table->integer('amount')->default('0');
            $table->timestamps();
			$table->softDeletes();
        });
		
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
			$table->integer('sender_id');
            $table->integer('receiver_id');	
            $table->string('body');
            $table->timestamps();
			$table->softDeletes();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->id();
			$table->date('day');
            $table->double('rate',7,0); 		
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
