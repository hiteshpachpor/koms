<?php

use Illuminate\Database\Seeder;
use App\User;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Seed a few user addresses
        for ($i = 1; $i <= 10; $i++) {
            $user = User::find($i);

            DB::table('user_address')->insert([
                'user_id' => $i,
                'name' => $user->name,
                'phone' => $user->phone,
                'flat' => $faker->buildingNumber,
                'building' => $faker->secondaryAddress,
                'street' => $faker->streetName,
                'city' => $faker->city,
                'state' => $faker->state,
                'country' => $faker->country,
                'zipcode' => $faker->postcode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
