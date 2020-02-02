<?php

namespace App\Http\Controllers\Api;

use App\AppDefault;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Order;
use App\User;
use App\UserAddress;
use App\UserDetail;
use Auth;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = User::select('id',
                                 'fname',
                                 'lname',
                                 'phone',
                                 'email',
                                 'created_at')
                        ->where('id',Auth::id())
                        ->with(
                            'details:user_id,description,photo,referral_id',
                            'addresses:id,user_id,name,area_id,map_coordinates,building_community,type,appartment_no,remarks,is_default'
                        )
                        ->first();
        if($customer->details->photo)
            $customer->details->photo = asset('files/users/'.Auth::id().'/'.$customer->details->photo);
        else
            $customer->details->photo = null;

        $appDefaults = AppDefault::first();
        $orderTime = $appDefaults->order_time;

        $offers = Offer::select('id','name','image','description')->where('status',1)->get();
        $offerUrl = asset('files/offer_banners/');
        
        $collection = collect([
            'customer'  => $customer,
            'orderTime' => $orderTime,
            'offers'    => $offers,
            'offerUrl'  => $offerUrl,
            'notificationCount' => User::find(Auth::id())->unreadNotifications->count()
        ]);
        return response()->json($collection);
    }

    public function updateProfile(Request $request)
    {
        if ($request->fname || $request->lname) {
            $msgs = [
                "fname.required" => "First Name Cannot be empty"
            ];
            $validator = Validator::make($request->all(), [
                "fname" => ['required', 'string', 'max:255'],
            ],$msgs);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $input = $request->only('fname', 'lname');
            $address = User::where('id',Auth::id())->update($input);

            return response()->json([
                'status' => '200',
                'message'=> 'Profile Updated Successfully' 
            ],200);
        }

        if ($request->email) {
            $user = User::findOrFail(Auth::id());
            if($user->email==$request->email){
                return response()->json([
                    'status' => '200',
                    'message'=> 'Same Email Detected' 
                ],200);
            }
            
            $validator = Validator::make($request->all(), [
               'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $input = $request->only('email');
            $address = $user->update($input);

            return response()->json([
                'status' => '200',
                'message'=> 'Email Updated Successfully' 
            ],200);
        }
        
        //Save User Photo 
        if ($request->hasFile('photo')) {
            $validator = Validator::make($request->all(), [
                "photo" => 'mimes:jpeg,jpg,bmp,png|max:15072',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }


            $image = Image::make($request->file('photo'))->orientate();
            // prevent possible upsizing
            $image->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $photo = $request->file('photo');
            // $fileName = 'dp_user_'.Auth::id().'.'.$photo->getClientOriginalExtension();
            $fileName = 'dp_user_'.Auth::id().'.jpg';
            $uploadDirectory = public_path('files'.DS.'users'.DS.Auth::id());
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);
            // $photo->move($uploadDirectory, $fileName);

            $userDetail = UserDetail::updateOrCreate(
                ['user_id' => Auth::id()],
                ['photo' => $fileName]
            );

            return response()->json([
                'status' => '200',
                'message'=> 'Photo Updated Successfully', 
                'url' => asset('files/users/'.Auth::id().'/'.$fileName)
            ],200);
        } 

        return response()->json([
                    'status' => '400',
                    'message' => 'No parameters found to complete the request'
                ], 400);
    }

    public function changePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "newPhone" => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $error,
            ], 422);
        }

        $OTP = rand(1000,9999);
        $OTP_timestamp = Date('Y-m-d H:i:s');
        User::where('id',Auth::id())->update([
                        'OTP' => $OTP,
                        'OTP_timestamp' => $OTP_timestamp
                    ]);
        return response()->json([
                'status' => '200',
                'message'=> 'OTP has been sent to your phone', 
                'OTP'=> $OTP, 
            ],200);
    }

    public function updatePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newPhone' => 'required',
            'OTP' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::find(Auth::id());
        $OTP_timestamp = \Carbon\Carbon::parse($user->OTP_timestamp);
        $current = \Carbon\Carbon::now();
        $totalTime = \Carbon\Carbon::now()->diffInMinutes($OTP_timestamp);

        if($user->OTP==$request->OTP && $totalTime<=config('settings.OTP_expiry')){
            User::where('id',Auth::id())->update([
                        'phone' => $request->newPhone,
                    ]);
            return response()->json([
                'status' => '200',
                'message'=> 'Phone Number has been updated successfully', 
            ],200);
        }
        else{
            return response()->json([
                'status' => '403',
                'message'=> 'OTP did not match or may have expired', 
            ],403);
        }
        
    }

}
