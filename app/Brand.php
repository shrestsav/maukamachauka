<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'description',
        'url',
        'logo',
        'email',
        'cp_name',
        'cp_designation',
        'cp_contact',
        'status',
    ];

    protected $appends = ['logo_src', 'followed_status'];

    protected $hidden = ['followed_status'];

    public function getLogoSrcAttribute()
    {
        $no_image = "https://dummyimage.com/600x400/6e6e6e/ffffff&text=NO+IMAGE";
  		$src = $this->logo ? asset('files/brands/'.$this->logo) : $no_image;

        return $src;
    }

    public function getFollowedStatusAttribute()
    {
        return $this->followedBy->contains(Auth::id());
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'brand_category');
    }

    /**
     * Followed by Users
     */
    public function followedBy()
    {
        return $this->belongsToMany(User::class,'user_brand_preference');
    } 

    public function addresses()
    {
        return $this->hasMany(BrandAddress::class);
    }

    public function banners()
    {
        return $this->hasMany(BrandBanner::class);
    }
}
