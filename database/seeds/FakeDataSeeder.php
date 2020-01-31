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

        $totalCat = 10;
        $totalBrand = 20;
        $totalOffer = 200;

        for ($i = 1; $i <= $totalCat; $i++) {
            $cat = Category::create([ 
                'name' 		  => $faker->name,
                'description' => $faker->text,
                'image' 	  => 'empty',
                'status' 	  => 1,
            ]);
        }

        for ($i = 1; $i <= $totalBrand; $i++) {
            $brand = Brand::create([ 
                'name' 		  => $faker->name,
                'description' => $faker->text,
                'logo' 		  => 'empty',
                'status' 	  => 1,
            ]);
            // $randCatIDs = [];
            // $arrLength = rand(1,4);
            // for($d = 0; $d<$arrLength; $d++){
            //     array_push($randCatIDs, rand(1,$totalCat));
            // }
            $brand->categories()->attach(rand(1,$totalCat));
        }

        for ($i = 1; $i <= $totalOffer; $i++) {
            $offer = Offer::create([ 
                'brand_id'    => rand(1,$totalBrand),
                'title'       => $faker->name,
                'description' => $faker->text,
                'image' 	  => 'empty',
                'status' 	  => 1,
            ]);
            $offer->categories()->attach(rand(1,$totalCat));
        }

    }
}
