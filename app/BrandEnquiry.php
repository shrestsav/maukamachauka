<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandEnquiry extends Model
{
    protected $fillable = [
        'user_id',
        'brand_id',
        'offer_id',
        'message'
    ];
}
