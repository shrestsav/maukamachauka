<?php

namespace App\Http\Controllers;

use App\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::with('categories')->paginate(10)->makeVisible('image_src');
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
        $validatedData = $request->validate([
            'title'        => 'required|max:20|unique:categories',
            'description'  => 'required|max:100',
            'image_file'   => 'required|mimes:jpeg,bmp,png|max:15360',
            'expires_in'   => 'required',
            'categories'   => 'required|array',
        ]);

        $offer = new Offer();
        $offer->title = $request->title;
        $offer->description = $request->description;
        $offer->expires_in = $request->expires_in;
        $offer->save();

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
        //
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
