<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $appends = ['logo_src'];

    public function getLogoSrcAttribute()
    {
  		// $src = $this->image ? asset('files/categories/'.$this->image) : asset('files/categories/no_image.png');
  		
        return "https://loremflickr.com/320/240?".Str::random(5);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'brand_category');
    }
}
