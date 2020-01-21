<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Brand;
use App\Offer;

class TestController extends Controller
{
	public function offers()
    {
        $offers = Offer::paginate(10);

        return response()->json($offers);
    }

}
