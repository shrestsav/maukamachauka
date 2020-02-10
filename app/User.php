<?php

namespace App;

use App\Notifications\OTPNotification;
use App\Notifications\SystemNotification;
use App\Notifications\AppNotification;
use App\Traits\NotificationLogics;
use App\Order;
use Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use NotificationLogics;
    use HasApiTokens, Notifiable;
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname','lname', 'email', 'username', 'password','phone','OTP','OTP_timestamp','f_id','g_id','avatar'
    ];

    protected $appends = ['full_name','user_avatar'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','user_avatar','full_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }

    public function getUserAvatarAttribute()
    {
        $url = '';
        if($this->photo!=''||$this->photo!=null)
            $url = asset('files/users/'.Auth::id().'/'.$this->photo);
        elseif($this->avatar)
            $url = $this->avatar;
        else
            $url = asset('files/users/no_photo.png');
        
        return $url;
    }

    /**
    * Validate the password of the user for the Passport password grant.
    *
    * @param  string $password
    * @return bool
    */
    public function validateForPassportPasswordGrant($email)
    {
        if($this->email==$email)
            return true;
        else
            return false;
    }

    public function sendOTP()
    {
        $OTP = $this->OTP;
        $this->notify(new OTPNotification($OTP));
    }

    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * Send Push Notifications
     *
     * @param  array  $notification
     * @return boolean
     */
    public function pushNotification($notification)
    {   
        $this->notify(new SystemNotification($notification));
    }

    /**
     * Store App Notifications on Database
     *
     * @param  array  $notification
     * @return boolean
     */
    public function AppNotification($notification)
    {   
        $this->notify(new AppNotification($notification));
    }

    /**
     * Favorite Offers of all users
     */
    public function favoriteOffers()
    {
        return $this->belongsToMany(Offer::class,'user_favorite_offer');
    }

    /**
     * Subscribed tags
     */
    public function tagsPreferences()
    {
        return $this->belongsToMany(Category::class,'user_tags_preference');
    }

    /**
     * Followed Brands
     */
    public function followedBrands()
    {
        return $this->belongsToMany(Brand::class,'user_brand_preference');
    }
}