<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BrandBanner extends Model
{
    protected $fillable = [
        'brand_id',
        'image'
    ];

    protected $appends = ['image_src'];
    
    public function getImageSrcAttribute()
    {	
        return "https://loremflickr.com/320/240?".Str::random(5);
    }
}
