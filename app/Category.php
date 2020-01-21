<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
  		
        return "https://loremflickr.com/320/240";
    }
}
