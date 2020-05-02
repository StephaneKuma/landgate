<?php

namespace App\Models;

use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'name', 'size'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
