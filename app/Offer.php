<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
	protected $fillable = ['category_id','brand_id','title','description','image','status'];

    protected $appends = ['image_src'];

    public function getImageSrcAttribute()
    {
  		// $src = $this->image ? asset('files/categories/'.$this->image) : asset('files/categories/no_image.png');
  		
        return "https://loremflickr.com/320/240";
    }

    /**
     * The category that belongs to the offer.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The brand that belongs to the offer.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    } 
}
