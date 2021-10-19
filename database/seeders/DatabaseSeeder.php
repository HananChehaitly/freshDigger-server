<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
			"email" => "hanan@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 1,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);
    }
}
