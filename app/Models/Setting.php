<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'footer',
        'about_us',
        'facebook',
        'twitter',
        'linkedin'
    ];
}
