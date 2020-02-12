<?php

namespace App\Http\Controllers;

use App\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offers = Offer::with('categories','brand');

        if($request->brand_id)
            $offers->where('brand_id',$request->brand_id);

        $offers = $offers->orderBy('created_at', 'DESC')->paginate(10);
        
        $offers->setCollection( $offers->getCollection()->makeVisible('image_src'));

        return response()->json($offers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->all() as $key => $req){
            if($req=="null"){
                $request[$key] = null;
            }
        }
        $request['categories'] = json_decode($request['categories']);
        $request['brand'] = json_decode($request['brand'], true);
        $validatedData = $request->validate([
            'brand'        => 'required|array',
            'title'        => 'required|max:50',
            'description'  => 'required|max:500',
            'image_file'   => 'required|mimes:jpeg,bmp,png|max:15360',
            'expires_in'   => 'required|date',
            'categories'   => 'required|array',
        ]);

        if($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $fileName = Str::uuid().'.'.$image->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'offers');
            $image->move($uploadDirectory, $fileName);
        } 

        $offer = new Offer();
        $offer->brand_id = $request->brand['id'];
        $offer->title = $request->title;
        $offer->description = $request->description;
        $offer->expires_in = $request->expires_in;
        $offer->image = $fileName;
        $offer->source = $request->source;
        $offer->save();

        //Attaching categories to the offers
        $cat_ids = collect($request['categories'])->pluck('id');
        $offer->categories()->attach($cat_ids);

        //Attach Brand to offer
        
        return response()->json([
            'message'  =>  'Offer saved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
