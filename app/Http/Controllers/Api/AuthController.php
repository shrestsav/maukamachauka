<?php

namespace App\Http\Controllers\Api;

use App\DeviceToken;
use App\Http\Controllers\Controller;
use App\Notifications\OTPNotification;
use App\Role;
use App\User;
use App\UserDetail;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

use Mail;
use App\Mail\notifyMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|string|email|max:255|unique:users',
            'fname'        => 'required|string|max:255',
            'lname'        => 'required|max:255|string',
            'password'     => 'required|max:255|string',
            'device_id'    => 'required|max:255',
            'device_token' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }
            
        $user = User::create([
            'fname' => $request->name,
            'lname' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role_id = Role::where('name','user')->first()->id;

        //Assign User as user
        $user->attachRole($role_id);

        User::notifyNewRegistration($user);

        $response = $this->generateToken($request->email, $request->device_id, $request->device_token);
        
        return response()->json([
            'message'   =>  'Verification Email has been sent'
        ]);
    }

    public function login(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'email'        => 'required|string|email|max:255|exists:users',
            'password'     => 'required|max:255|string',
            'device_id'    => 'required',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => '422',
                'message' => 'Validation Failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email',$request->email)->first();

        if(!Hash::check($request['password'], $user->getAuthPassword())){
            return response()->json([
                'status'   =>  '401',
                'message'  =>  'Password doesnot match'
            ],401);
        }
        
        if(is_null($user->email_verified_at)){
            return response()->json([
                'status'   =>  '403',
                'message'  =>  'Your email has not been verified yet. You can request for another verification email.',
                'url'      =>   url('/request-verification/'.$user->id)
            ],401);
        }

        $response = $this->generateToken($request->email, $request->device_id, $request->device_token);

        return response()->json($response);
    }

    public function generateToken($email, $device_id, $device_token)
    {
        $url = url('').'/oauth/token';
        $user = User::where('email',$email)->firstOrFail();
    
        $http = new \GuzzleHttp\Client();
        $response = $http->post(url('').'/oauth/token', [
                        'form_params' => [
                            'grant_type'    => 'password',
                            'client_id'     => 2,
                            'client_secret' => 'iMW9m2tB1h2RMANtE0DPCcNrZs6nG8yRFaBwK3y5',
                            'username'      => $email,
                            'password'      => $email,
                            'scope'         => '',
                        ],
                        'http_errors' => false // add this to return errors in json
                    ]);

        $token_response = json_decode((string) $response->getBody(), true);

        
        $check = DeviceToken::where('device_id',$device_id)
                            ->where('device_token',$device_token);

        //If both device_id and device_token exists                           
        if($check->exists()){
            $check->update([
                'user_id'  =>  $user->id
            ]);
        }

        // If device_id only exists
        else{
            $deviceToken = DeviceToken::updateOrCreate(
                        [
                            'device_id' => $device_id
                        ],
                        [
                            'user_id'      => $user->id,
                            'device_token' => $device_token
                        ]
                    );
        }

        $result = [
            'tokens'   =>  $token_response,
            'user_id'  =>  $user->id
        ];

        return $result;
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client'       => 'required|string|max:50',
            'token'        => 'required|string|max:2000',
            'device_id'    => 'required',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => '422',
                'message' => 'Validation Failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userSocialProfile = false;
        $client = $request->client;
        $token = $request->token;

        if($client=='google'){
            $social_id = 'g_id';
            $profile = $this->googleLoginInfo($token);
        }

        if($client=='facebook'){
            $social_id = 'f_id';
            $profile = $this->facebookLoginInfo($token);
        }

        if($profile){
            $user = User::where('email', $profile['email'])->orWhere($social_id, $profile['id']);
            if($user->exists()){
                $user = $user->first();
                $user->update([
                    $social_id => $profile['id'],
                    'avatar'   => $profile['avatar']
                ]);
            }
            else{
                $user = User::create([
                    'email'     =>  $profile['email'],
                    $social_id  =>  $profile['id'],
                    'fname'     =>  $profile['fname'],
                    'lname'     =>  $profile['lname'],
                ]);
            }
            $response = $this->generateToken($user->email, $request->device_id, $request->device_token);
        }
        else{
            return response()->json([
                'status'  => '400',
                'message' => 'Problem with Token',
            ], 400);
        }
        

        return $response;
        // $existingUser = User::where('email', $user->getEmail())->orWhere($social_id, $user->id)->first();
    }

    public function googleLoginInfo($token)
    {
        $curlUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token='.$token;

        $userSocialProfile = $this->curlRequest($curlUrl);

        if(!$userSocialProfile){
            return false;
        }

        else{
            $user = [
                'id'      =>  $userSocialProfile['sub'],
                'fname'   =>  $userSocialProfile['given_name'],
                'lname'   =>  $userSocialProfile['family_name'],
                'avatar'  =>  $userSocialProfile['picture'],
                'email'   =>  $userSocialProfile['email']
            ];
            
            return $user;
        }
    }

    public function facebookLoginInfo($token)
    {
        $curlUrl = 'https://graph.facebook.com/me?fields=email,name,first_name,last_name,picture&access_token='.$token;

        $userSocialProfile = $this->curlRequest($curlUrl);

        if(!$userSocialProfile){
            return false;
        }

        else{
            $user = [
                'id'      =>  $userSocialProfile['id'],
                'fname'   =>  $userSocialProfile['first_name'],
                'lname'   =>  $userSocialProfile['last_name'],
                'avatar'  =>  $userSocialProfile['picture']['data']['url'],
                'email'   =>  $userSocialProfile['email']
            ];
            
            return $user;
        }
    }

    public function curlRequest($curlUrl){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, (array)400);

        $headers = array();
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    break;
                default:
                    return false;
            }
        }
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        return json_decode($result, true);
    }
    public function createProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userInput = $request->only('fname', 'lname', 'email');

        if($request->referred_by){
            $check = UserDetail::where('referral_id',$request->referred_by);
            if(!$check->exists()){
                return response()->json([
                    'status' => '404',
                    'message'=> 'Referral ID is Invalid' 
                ],404);
            }
        }

        $address = User::where('id',Auth::id())->update($userInput);
        
        if($request->email){
            User::notifyNewRegistration(Auth::id());
        }
        //Generate random Referral ID for registered user
        $random_string = substr($request->fname, 0, 3).rand(100,999).Str::random(10);
        $referral_id = strtoupper(substr($random_string, 0, 8));
        
        //Save User Photo 
        $userDetail = UserDetail::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'referred_by' => $request->referred_by,
                    'referral_id' => $referral_id
                ]);

        return response()->json([
                'status' => '200',
                'message'=> 'Profile Created Successfully' 
            ],200);
    }

    public function checkRole()
    {
        $role = Auth::user()->roles()->first()->name;

        return response()->json([
            'user_id' => Auth::id(),
            'role' => $role
        ]);
    }

    public function tokens()
    {
        return User::find(Auth::id())->tok();
    }

    public function notifications()
    {
        $user = User::find(Auth::id());

        //First Mark All Notifications as read
        $user->unreadNotifications()->update(['read_at' => now()]);

        // Return notifications 
        return response()->json($user->notifications->take(50));
    }

    public function countUnreadNotifications()
    {
        return response()->json(User::find(Auth::id())->unreadNotifications->count());
    }


    public function markAsRead($notificationId)
    {
        $user = User::find(Auth::id());

        $notification = $user->unreadNotifications->find($notificationId);
        if($notification){
            $notification->markAsRead();
            return response()->json([
                'status' => '200',
                'message'=>'Notifications Marked as read'
            ],200);
        }
        return response()->json([
            'status' => '404',
            'message'=>'Notification Not Found'
        ],404);
    }

    public function markAllAsRead()
    {
        $user = User::find(Auth::id());

        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'status' => '200',
            'message'=>'All Notifications Marked as read'
        ],200);
    }

    public function removeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DeviceToken::where('device_token',$request->device_token)->delete();

        return response()->json([
            'status' => '200',
            'message'=>'Device Token Removed'
        ],200);
    }

}
