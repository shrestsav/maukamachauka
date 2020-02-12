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
  		// $src = $this->image ? asset('files/categories/'.$this->image) : asset('files/categories/no_image.png');
  		
        return "https://loremflickr.com/320/240?".Str::random(5);
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
