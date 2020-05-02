<?php

namespace App\Models;

use App\User;
use App\Models\Rating;
use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'price',
        'featured',
        'purpose',
        'type',
        'image',
        'bedroom',
        'bathroom',
        'city',
        'city_slug',
        'address',
        'area',
        'agent_id',
        'description',
        'video',
        'floor_plan',
        'location_latitude',
        'location_longitude',
        'nearby'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class);
    }

    public function property_images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
