<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $faker = Factory::create('IR-fa');
        DB::table('users')->insert([
            'name' => $faker->name().' '.$faker->lastName(),
            'email' => $faker->email(),
            'password' => Hash::make('password'),
            'verified' => 1,
            'verify_code' => 11111,
            'mobile' => '0935'.random_int(1111111,9999999)
        ]);
    }
}
