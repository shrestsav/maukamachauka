<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

class Offer extends Model
{
	protected $fillable = ['category_id','brand_id','title','description','image','status','liked_by'];

    protected $appends = ['image_src','liked_status'];

    protected $hidden = ['liked_status'];

    protected $casts = [
        'liked_by'  => 'array',
        'location'  => 'array',
        'expires_in'=> 'datatime'
    ];

    public function getLikedStatusAttribute($value)
    {
        return is_array($this->liked_by) && in_array(Auth::id(), $this->liked_by);
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
}
