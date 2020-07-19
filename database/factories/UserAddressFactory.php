<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserAddress;
use Faker\Generator as Faker;

$factory->define(UserAddress::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->name,
        'phone' => $faker->e164PhoneNumber,
        'flat' => $faker->buildingNumber,
        'building' => $faker->secondaryAddress,
        'street' => $faker->streetName,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'zipcode' => $faker->postcode,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
