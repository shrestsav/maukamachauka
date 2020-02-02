<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

class Offer extends Model
{
	protected $fillable = ['category_id','brand_id','title','description','image','status','liked_by'];

    protected $appends = ['image_src','likes_count','liked_status','favorite_status'];

    protected $hidden = ['likes_count','liked_status','favorite_status','pivot','status','liked_by','image','userFavorites'];

    protected $casts = [
        'brand_id'  => 'int',
        'status'    => 'int',
        'liked_by'  => 'array',
        'location'  => 'array',
        'expires_in'=> 'datatime'
    ];

    public function getLikedStatusAttribute($value)
    {
        return is_array($this->liked_by) && in_array(Auth::id(), $this->liked_by);
    }

    public function getLikesCountAttribute($value)
    {
        $count = 0;

        if(is_array($this->liked_by)){
            $count = count($this->liked_by);
        }

        return $count;
    }

    public function getFavoriteStatusAttribute($value)
    {
        return $this->userFavorites->contains(Auth::id());
    }

    public function getImageSrcAttribute()
    {
  		// $src = $this->image ? asset('files/categories/'.$this->image) : asset('files/categories/no_image.png');
  		
        return "https://loremflickr.com/320/240?".Str::random(5);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'offer_category');
    }

    /**
     * The brand that belongs to the offer.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    } 

    /**
     * Users who favorite the offers
     */
    public function userFavorites()
    {
        return $this->belongsToMany(User::class,'user_favorite_offer');
    } 
}
