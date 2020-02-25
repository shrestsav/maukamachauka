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
     * Display Listing of offers
     *
    **/
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

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status','likes_count']));

        $categories = Category::select('id','name','image')->where('status',1)->get();

        return response()->json([
            'offers'      =>  $offers,
            'categories'  =>  $categories
        ]);
    }

    /**
     * Display Brand Details
     *
    **/
    public function brandDetails($brandID)
    {
        $brand = Brand::with('categories','banners:brand_id,image')->findOrFail($brandID)->makeVisible('followed_status')->makeHidden('followedBy');
        
        return response()->json($brand);
    }

    /**
     * Show all offers of brand
     *
    **/
    public function brandOffers($brandID)
    {
        $brand = Brand::findOrFail($brandID);

        $offers = Offer::with('categories:id,name','brand:id,name')->where('brand_id', $brandID)->where('expires_in','>=',Date('Y-m-d H:i:s'))->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status','likes_count']));
        
        return response()->json($offers);
    }

    /**
     * Show all offers of category
     *
    **/
    public function categoryOffers($catID)
    {
        $offers = Category::findOrFail($catID)->offers()->with('categories:id,name','brand:id,name')->paginate(config('settings.rows'));

        $offers->setCollection( $offers->getCollection()->makeVisible(['liked_status','favorite_status','likes_count']));

        return response()->json($offers);
    }

    /**
     * Add Like to Offer
     *
    **/
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
                'message'       =>  'Offer marked as like'
            ]);
        }
        
        return response()->json([
            'message'   =>  "Nothing actually happened"
        ], 403);  

    }

    /**
     * Remove Like From Offer
     *
    **/
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

    /**
     * Search Offers
     *
    **/
    public function search(Request $request)
    {
        $offers = Offer::search($request->search)->paginate(20);

        $offers->setCollection( $offers->getCollection()->load('categories:id,name','brand:id,name')->makeVisible(['liked_status','favorite_status','likes_count']));
        
        return response()->json($offers); 
    }

    /**
     * Search Offers TEST
    **/
    public function searchTEST($search)
    {
        $offers = Offer::search($search)->paginate(20);

        $offers->setCollection( $offers->getCollection()->load('categories:id,name','brand:id,name')->makeVisible(['liked_status','favorite_status','likes_count']));
        
        return response()->json($offers); 
    }

}
