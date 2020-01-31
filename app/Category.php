<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','description','image','status'];

    protected $appends = ['image_src'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // /**
    //  * Get the can_delete flag for users.
    //  *
    //  * @return status
    //  */
    // public function getCanDeleteAttribute()
    // {
    //     $status = true;

    //     if(count($this->items)){
    //         $status = false;
    //     }
        
    //     return $status;
    // }    

    public function getImageSrcAttribute()
    {
  		// $src = $this->image ? asset('files/categories/'.$this->image) : asset('files/categories/no_image.png');
  		
        return "https://loremflickr.com/320/240?".Str::random(5);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class,'brand_category');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class,'offer_category');
    }
}
