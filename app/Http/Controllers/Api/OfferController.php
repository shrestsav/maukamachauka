<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Category;
use App\Brand;
use App\Offer;
use Auth;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::select('id',
                                'brand_id',
                                'title',
                                'description',
                                'liked_by',
                                'created_at')
                        ->with('categories:id,name','brand:id,name')
                        ->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status']));

        $categories = Category::select('id','name','image')->where('status',1)->get();

        return response()->json([
            'offers'      =>  $offers,
            'categories'  =>  $categories
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function brandDetails($brandID)
    {
        $brand = Brand::findOrFail($brandID);
        
        return response()->json($brand);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function brandOffers($brandID)
    {
        $brand = Brand::findOrFail($brandID);

        $offers = Offer::where('brand_id', $brandID)->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status']));
        
        return response()->json($offers);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function categoryOffers($catID)
    {
        $offers = Category::findOrFail($catID)->offers()->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status']));

        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function likeOffer($offerID)
    {
        $offers = Offer::findOrFail($offerID);

        $liked_by = [];

        if($offers->liked_by)
           $liked_by = $offers->liked_by;
        
        if(!in_array(Auth::id(), $liked_by))
            array_push($liked_by, Auth::id());

        if(count($liked_by)){
            $offers->update([
                'liked_by'  =>  $liked_by
            ]);

            return response()->json([
                'message'   =>  'Offer marked as like'
            ]);
        }
        
        return response()->json([
            'message'   =>  "Nothing happened actually"
        ]);  

    }

}
