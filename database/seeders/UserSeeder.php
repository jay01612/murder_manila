<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $themes1 = DB::table('themes')->insert([
        	'name' => "Revenge At The Reunion"
        ]);

        $themes2 = DB::table('themes')->insert([
        	'name' => "Murder At The Mansion"
        ]);

        $position = DB::table('access_levels')->insert([
    		"access_name" => "admin"
    	]);

    	$user = DB::table('users')->insert([
    		"fname" => "admin",
    		"lname" => "admin",
            "username" => "admin",
    		"password" => bcrypt('password'),
    		"email" => "admin@test.com",
    		"position_id" => 1
    	]);
        
    }
}
