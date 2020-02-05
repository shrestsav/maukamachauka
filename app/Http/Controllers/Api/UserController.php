<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function profile()
    {
        $user = User::select('id','fname','lname','phone','email','photo','avatar')->find(Auth::id())->makeVisible(['full_name','user_avatar']); 

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        if ($request->fname || $request->lname || $request->phone) {
            $msgs = [
                "fname.required" => "First Name Cannot be empty"
            ];
            $validator = Validator::make($request->all(), [
            	'fname' =>  'required|string|max:255',
            	'phone' =>  'required|unique:users,phone,'.Auth::id()
            ],$msgs);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '422',
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $input = $request->only('fname', 'lname', 'phone');
            $update = User::where('id',Auth::id())->update($input);

            return response()->json([
                'status' => '200',
                'message'=> 'Profile Updated Successfully' 
            ], 200);
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

            $fileName = 'DP_'.Auth::id().'_'.Str::random(8).'.jpg';
            $uploadDirectory = public_path('files'.DS.'users'.DS.Auth::id());
            if (!file_exists($uploadDirectory)) {
                \File::makeDirectory($uploadDirectory, 0755, true);
            }
            $image->save($uploadDirectory.DS.$fileName,60);
            // $photo->move($uploadDirectory, $fileName);

            $update = User::where('id',Auth::id())->update([
            	'photo'	=> $fileName
            ]);

            return response()->json([
                'status' => '200',
                'message'=> 'Photo Updated Successfully', 
                'url' => asset('files/users/'.Auth::id().'/'.$fileName)
            ], 200);
        } 

        return response()->json([
            'status' => '400',
            'message' => 'No parameters found to complete the request'
        ], 400);
    }

    /**
     * Subscribed Tags
     *
    **/
    public function subscribedTags()
    {
        $tags = Auth::user()->tagsPreferences()->get();
        
        return response()->json($tags);  
    }

    /**
     * Add tags to User preferences
     *
    **/
    public function subscribeTag($catID)
    {
        $tag = Category::findOrFail($catID);

        if($tag->tagsUsers->contains(Auth::id())){
            return response()->json([
                'message'   =>  "You've already subscribed"
            ], 403); 
        }

        $tag->tagsUsers()->attach(Auth::id());
        
        return response()->json([
            'response_type' =>  'subscribe_tag',
            'message'       =>  "Successfully Subscribed"
        ]);  
    }

    /**
     * Remove tags from User preferences
     *
    **/
    public function unSubscribeTag($catID)
    {
        $tag = Category::findOrFail($catID);

        if($tag->tagsUsers->contains(Auth::id())){
            $tag->tagsUsers()->detach(Auth::id());
            return response()->json([
                'response_type' =>  'subscribe_tag',
                'message'       =>  "Successfully Unsubscribed"
            ]); 
        }

        return response()->json([
            'response_type' =>  'subscribe_tag',
            'message'   =>  "You havenot subscribed this tag in the first place to unsubscribe"
        ], 403); 

    }
}
