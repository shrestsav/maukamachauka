<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\User;
use App\Brand;
use App\UserDetail;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function profile()
    {
        $user = User::select('id','fname','lname','phone','email','photo','avatar')->with('details:user_id,gender,dob')->find(Auth::id())->makeVisible(['full_name','user_avatar']); 

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "photo"  => 'nullable|mimes:jpeg,jpg,bmp,png|max:15072',
            "dob"    => 'nullable|date',
            "gender" => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        if ($request->fname || $request->lname || $request->phone || $request->dob || $request->gender) {
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

            $updateDetails = UserDetail::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'dob'    => $request->dob,
                    'gender' => $request->gender
                ]
            );
            return response()->json([
                'status' => '200',
                'message'=> 'Profile Updated Successfully' 
            ], 200);
        }

        //Save User Photo 
        if ($request->hasFile('photo')) {
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
        $tags = Auth::user()->tagsPreferences()->get()->makeHidden(['description','image','status','created_at','updated_at','image_src','pivot']);
        
        return response()->json($tags);  
    }

    /**
     * Not Subscribed Tags
     *
    **/
    public function notSubscribedTags()
    {
        $tags = Category::whereDoesntHave('tagsUsers', function ($query) {
                            $query->where('id', Auth::id());
                        })
                        ->get()
                        ->makeHidden(['description','image','status','created_at','updated_at','image_src','pivot']);
        
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
    /**
     * Follow Brand
     *
    **/
    public function followBrand($brandID)
    {
        $brand = Brand::findOrFail($brandID);

        if($brand->followedBy->contains(Auth::id())){
            return response()->json([
                'message'   =>  "You've already followed this brand"
            ], 403); 
        }

        $brand->followedBy()->attach(Auth::id());
        
        return response()->json([
            'response_type' =>  'follow_brand',
            'message'       =>  "Successfully Followed Brand"
        ]);  
    }

    /**
     * Unfollow Brand
     *
    **/
    public function unfollowBrand($brandID)
    {
        $brand = Brand::findOrFail($brandID);

        if($brand->followedBy->contains(Auth::id())){
            $brand->followedBy()->detach(Auth::id());
            return response()->json([
                'response_type' =>  'follow_brand',
                'message'       =>  "Successfully Unfollowed Brand"
            ]); 
        }

        return response()->json([
            'response_type' =>  'follow_brand',
            'message'   =>  "You havenot followed this brand in the first place to unfollow"
        ], 403); 

    }

    /**
     * Followed Brands
     *
    **/
    public function followedBrands()
    {
        $brands = Auth::user()->followedBrands()->paginate(10);
        $brands->setCollection( $brands->getCollection()->makeHidden(['description','logo','status','created_at','updated_at','pivot']));
        return response()->json($brands);  
    }
}
