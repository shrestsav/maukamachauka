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
                                'expires_in',
                                'location',
                                'liked_by',
                                'created_at')
                        ->where('expires_in','>=',Date('Y-m-d H:i:s'))
                        ->with('categories:id,name','brand:id,name')
                        ->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status'])->makeHidden('userFavorites'));

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

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status']));
        
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

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status']));

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
        $offer = Offer::findOrFail($offerID);

        $liked_by = [];

        if($offer->liked_by)
           $liked_by = $offer->liked_by;
        
        if(!in_array(Auth::id(), $liked_by)){
            array_push($liked_by, Auth::id());

            $offer->update([
                'liked_by'  =>  $liked_by
            ]);

            return response()->json([
                'response_type' =>  'like_offer',
                'liked_status'  =>  true,
                'message'       =>  'Offer marked as like'
            ]);
        }
        
        return response()->json([
            'message'   =>  "Nothing actually happened"
        ], 403);  

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeLikeOffer($offerID)
    {
        $offer = Offer::findOrFail($offerID);

        $liked_by = $offer->liked_by;

        if($liked_by && is_array($liked_by) && in_array(Auth::id(), $liked_by)){
            $arrKey = array_search(Auth::id(), $liked_by);
            array_splice($liked_by, $arrKey, 1);
            $offer->update([
                'liked_by'  =>  $liked_by
            ]);

            return response()->json([
                'response_type'   =>  'like_offer',
                'unliked_status'  =>  true,
                'message'         =>  "Removed Like from offer"
            ]);
        }
        
        return response()->json([
            'message'   =>  "Nothing actually happened"
        ], 403);  

    }

    /**
     * Add to Favorites
     *
    **/
    public function addToFavorites($offerID)
    {
        $offer = Offer::findOrFail($offerID);

        if($offer->userFavorites->contains(Auth::id())){
            return response()->json([
                'message'   =>  "You've already favorite this offer"
            ], 403); 
        }

        $offer->userFavorites()->attach(Auth::id());
        
        return response()->json([
            'response_type' =>  'favorite_offer',
            'message'       =>  "Offer added to favorites"
        ]);  
    }

    /**
     * Add to Favorites
     *
    **/
    public function removeFromFavorites($offerID)
    {
        $offer = Offer::findOrFail($offerID);

        if($offer->userFavorites->contains(Auth::id())){
            $offer->userFavorites()->detach(Auth::id());
            return response()->json([
                'response_type' =>  'favorite_offer',
                'message'       =>  "Offer removed from favorites"
            ]); 
        }

        return response()->json([
            'message'   =>  "Offer doesnot exists on your favorite list to unfavorite"
        ], 403); 

    }

    /**
     * User Favorite Offers
     *
    **/
    public function userFavoriteOffers()
    {
        $offers = Auth::user()->favoriteOffers()->paginate(config('settings.rows'));

        return response()->json($offers); 
    }

}
