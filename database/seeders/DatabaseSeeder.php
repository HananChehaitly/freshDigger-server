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

        DB::table("users")->insert([
			"email" => "glam@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 2,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("users")->insert([
			"email" => "foodlb@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 2,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("users")->insert([
			"email" => "university@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 2,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("users")->insert([
			"email" => "charbel@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 3,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("users")->insert([
			"email" => "ali@gmail.com",
            "password" => bcrypt("123"),
            "user_type_id" => 3,
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("businesses")->insert([
            "id" =>"2",
			"name" => "Glam Beirut",
            "bio" => "Help us buy the products we need!",
            "weekly_limit" => "300",
            "latitude" => "33.870140804564585", 
            "longitude" => "35.485188088585765",
            "picture_url" => "",
            "phone_number" => "70866128",
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("businesses")->insert([
			"id" => "3",
            "name" => "Food Company",
            "bio" => "Support us to keep providing for Lebanon!",
            "weekly_limit" => "250",
            "latitude" => "33.87351572572972", 
            "longitude" => "35.489536551367806",
            "picture_url" => "",
            "phone_number" => "70948763",
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);

        DB::table("businesses")->insert([
			"id" => "4",
            "name" => "University lb",
            "bio" => "Help us buy the right equipment for our labs!",
            "weekly_limit" => "300",
            "latitude" => "33.900723662597514", 
            "longitude" => "35.480365482178776",
            "picture_url" => "",
            "phone_number" => "70374834",
			"created_at" => date("Y-m-d"),
			"updated_at" => date("Y-m-d")
		]);


    }
}
