<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BoxOrder;
use Faker\Generator as Faker;

$factory->define(BoxOrder::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'user_address_id' => 1,
        'delivery_date' => $faker
            ->dateTimeBetween(
                $startDate = '-14 days',
                $endDate = '+14 days',
                $timezone = null
            )
            ->format('Y-m-d'),
        'delivery_slot' => 'Morning',
        'delivery_notes' => $faker->sentence(
            $nbWords = 5,
            $variableNbWords = true
        ),
    ];
});
