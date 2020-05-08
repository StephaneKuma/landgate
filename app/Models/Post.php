<?php

namespace App\Models;

use App\User;
use App\Models\Tag;
use App\Models\Comment;
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
        return $this->belongsTo(Category::class)->withTimestamps();;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();;
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function archives()
    {
        return static::selectRaw('year(created_at) year, monthname(created_at) month, count(*) published')
            ->groupBy('year','month')
            ->orderByRaw('min(created_at) desc')
            ->get()
            ->toArray();
    }
}
