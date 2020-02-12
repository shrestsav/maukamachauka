<?php

namespace App\Http\Controllers\Api;

use Auth;
use Mail;
use App\Brand;
use App\Offer;
use App\BrandEnquiry;
use App\Mail\notifyMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EnquiryController extends Controller
{
    public function sendEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "message"   => 'required|max:1000',
            "brand_id"  => 'nullable|numeric|min:0',
            "offer_id"  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $brand_id = $request->brand_id;
        $offer_id = $request->offer_id;

        if($brand_id)
            $brand = Brand::findOrFail($brand_id);
        elseif($offer_id){
            $brand = Offer::findOrFail($offer_id)->brand()->first();
            $brand_id = $brand->id;
        }
        
        //Store Enquiry in Database
        $enquiry = BrandEnquiry::create([
            'user_id'   =>  Auth::id(),
            'brand_id'  =>  $brand_id,
            'offer_id'  =>  $offer_id,
            'message'   =>  $request->message
        ]);
        
        //Send Email to Brand
        $mailData = [
            'emailType'  => 'brand_enquiry',
            'name'       => $brand->name,
            'email'      => $brand->email,
            'subject'    => "New Enquiry",
            'message'    => $request->message,
            'userEmail'  => Auth::user()->email,
            'userName'   => Auth::user()->full_name,
        ];
        
        // Notify user in email
        Mail::send(new notifyMail($mailData));

        return response()->json([
            'message'   =>  'Enquiry has been sent successfully'
        ]);
    }
}
