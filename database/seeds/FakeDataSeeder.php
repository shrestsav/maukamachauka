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
        DB::table('brands')->truncate();
        DB::table('brand_category')->truncate();
        DB::table('categories')->truncate();
        DB::table('offers')->truncate();
        DB::table('offer_category')->truncate();
        
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
                'name' 		        => $faker->name,
                'description'       => $faker->text,
                'url' 		        => 'https://brand.com',
                'logo' 		        => 'empty',
                'email'             => 'shrestsav@gmail.com',
                'cp_name'           => 'Likita',
                'cp_designation'    => 'CEO',
                'cp_contact'        => '9801020304',
                'logo' 		        => 'empty',
                'status' 	        => 1,
            ]);
            // $randCatIDs = [];
            // $arrLength = rand(1,4);
            // for($d = 0; $d<$arrLength; $d++){
            //     array_push($randCatIDs, rand(1,$totalCat));
            // }
            $brand->categories()->attach(rand(1,$totalCat));
        }

        $now = \Carbon\Carbon::now();

        for ($i = 1; $i <= $totalOffer; $i++) {
            $offer = Offer::create([ 
                'brand_id'    => rand(1,$totalBrand),
                'title'       => $faker->name,
                'description' => $faker->text,
                'image' 	  => 'empty',
                'status'      => 1,
                'expires_in'  => $now->addDays(rand(1,15)),
                'location' 	  => [
                    [
                        'name'  =>  'Kalimati',
                        'lat'   =>  (float)27.699051,
                        'long'  =>  (float)85.2876866
                    ]
                ]
            ]);
            $offer->categories()->attach(rand(1,$totalCat));
        }

    }
}
