<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

class Category extends Model
{
    protected $fillable = ['name','description','image','status'];

    protected $appends = ['image_src','subscribed_status'];

    protected $hidden = ['subscribed_status'];

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

    public function getSubscribedStatusAttribute($value)
    {
        return $this->tagsUsers->contains(Auth::id());
    }

    public function getImageSrcAttribute()
    {
        $no_image = "https://dummyimage.com/600x400/6e6e6e/ffffff&text=NO+IMAGE";
  		$src = $this->image ? asset('files/categories/'.$this->image) : $no_image;

        return $src;
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class,'brand_category');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'offer_category');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class,'offer_category');
    }

    /**
     * tags users
     */
    public function tagsUsers()
    {
        return $this->belongsToMany(User::class,'user_tags_preference');
    } 
}
