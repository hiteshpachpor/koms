<?php

use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
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

        // Seed a few suppliers
        for ($i = 0; $i < 5; $i++) {
            DB::table('supplier')->insert([
                'name' => $faker->company,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
