<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $restaurants = [
            [83, 1],
            [43, 9],
            [1, 34, 7],
            [2, 73, 61],

            [83, 1],
            [43, 9],
            [1, 34, 7],
        ];

        $i = 1;
        foreach ($restaurants as $restaurant)
        {
            foreach ($restaurant as $restaurant_id)
            {
                DB::table('restaurants')->insert([
                    'dish_id' => $i,
                    'restaurant_id' => $restaurant_id,
                    'created_at' => Carbon::now(),
                ]);
            }
            $i++;
        }

    }
}
