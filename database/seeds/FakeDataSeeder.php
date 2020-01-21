<?php

use Illuminate\Database\Seeder;
use Faker\Provider\Base;
use App\Category;
use App\Brand;
use App\Offer;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $totalCat = 12;
        $totalBrand = 50;
        $totalOffer = 200;

        for ($i = 1; $i <= $totalCat; $i++) {
            Category::create([ 
                'name' 		  => $faker->name,
                'description' => $faker->text,
                'image' 	  => 'empty',
                'status' 	  => 1,
            ]);
        }

        for ($i = 1; $i <= $totalBrand; $i++) {
            Brand::create([ 
                'category_id' => rand(1,$totalCat),
                'name' 		  => $faker->name,
                'description' => $faker->text,
                'logo' 		  => 'empty',
                'status' 	  => 1,
            ]);
        }

        for ($i = 1; $i <= $totalOffer; $i++) {
            Offer::create([ 
                'brand_id'    => rand(1,$totalBrand),
                'category_id' => rand(1,$totalCat),
                'name' 		  => $faker->name,
                'description' => $faker->text,
                'image' 	  => 'empty',
                'status' 	  => 1,
            ]);
        }
    }
}
