<?php

namespace App\Models;

use App\User;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'category_id', 
        'title', 
        'slug', 
        'image', 
        'body', 
        'view_count', 
        'status', 
        'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
}
