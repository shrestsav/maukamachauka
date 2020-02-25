<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Auth;

class Offer extends Model
{
    use Searchable;

	protected $fillable = ['category_id','brand_id','title','description','image','status','liked_by','source'];

    protected $appends = ['image_src','likes_count','liked_status','favorite_status'];

    protected $hidden = ['likes_count','liked_status','favorite_status','pivot','status','liked_by','image','userFavorites'];

    protected $casts = [
        'brand_id'  => 'int',
        'status'    => 'int',
        'liked_by'  => 'array',
        'location'  => 'array',
        'expires_in'=> 'datatime'
    ];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'offers';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['_geoloc'] = [
            'lat' => $array['location'][0]['lat'],
            'lng' => $array['location'][0]['long'],
        ];

        $array['brand'] = $this->brand->makeHidden(['created_at','updated_at']);
        $array['categories'] = $this->categories->makeHidden(['created_at','updated_at','pivot']);
        
        unset($array['created_at'], $array['updated_at']);

        return $array;
    }

    public function getLikedStatusAttribute($value)
    {
        return is_array($this->liked_by) && in_array(Auth::id(), $this->liked_by);
    }

    public function getLikesCountAttribute($value)
    {
        $count = 0;

        if(is_array($this->liked_by)){
            $count = count($this->liked_by);
        }

        return $count;
    }

    public function getFavoriteStatusAttribute($value)
    {
        return $this->userFavorites->contains(Auth::id());
    }

    public function getImageSrcAttribute()
    {
        // $no_image = asset('files/categories/no_image.png');
        $no_image = "https://loremflickr.com/320/240?".Str::random(5);
  		$src = $this->image ? asset('files/offers/'.$this->image) : $no_image;
  		if($this->image=="empty"){
            $src = $no_image;
        }

        return $src;
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

    /**
     * Users who favorite the offers
     */
    public function userFavorites()
    {
        return $this->belongsToMany(User::class,'user_favorite_offer');
    } 
}
