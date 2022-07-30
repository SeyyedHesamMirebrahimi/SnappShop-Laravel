<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('IR-fa');
        return [
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'name' => $faker->name().' '.$faker->lastName(),
            'email' => $faker->email(),
            'password' => Hash::make('password'),
            'verified' => 1,
            'verify_code' => 11111,
            'mobile' => '0935'.random_int(1111111,9999999)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
