<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandBanner extends Model
{
    protected $fillable = [
        'brand_id',
        'image'
    ];
}
