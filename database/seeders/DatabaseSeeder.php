<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use DishTableSeeder;
// use RestaurantsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call(DishTableSeeder::class);
        $this->call(RestaurantsTableSeeder::class);
    }
}
