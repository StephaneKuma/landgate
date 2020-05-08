<?php

namespace App\Models;

use App\User;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Feature;
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

    public function features()
    {
        return $this->belongsToMany(Feature::class)->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function property_images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class, 'property_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
