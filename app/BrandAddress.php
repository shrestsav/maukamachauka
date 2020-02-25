<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandAddress extends Model
{
    protected $fillable = [
        'brand_id',
        'state',
        'city',
        'address',
        'coordinates'
    ];
}
