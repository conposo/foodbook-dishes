<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dishes = [
            ['cold', 'meatless', 'veggie'],
            ['hot', 'meat', 'chicken', 'pultry'],
            ['cold', 'meatless', 'veggie'],
            ['hot', 'meatless', 'mushroom'],


            ['veggie', 'fresh'],
            ['veggie', 'fresh'],
            ['veggie', 'fresh'],
        ];

        $i = 1;
        foreach ($dishes as $tags)
        {
            foreach ($tags as $tag)
            {
                DB::table('tags')->insert([
                    'dish_id' => $i,
                    'tag' => $tag,
                    'created_at' => Carbon\Carbon::now(),
                ]);
            }
            $i++;
        }

    }
}
