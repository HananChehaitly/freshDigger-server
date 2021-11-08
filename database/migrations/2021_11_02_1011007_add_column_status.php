<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatus extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {     
        Schema::table('notifications', function(Blueprint $table){
            $table->integer('status')->default('0');
        });
    }   
}
