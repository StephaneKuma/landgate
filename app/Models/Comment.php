<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'commentable_id',
        'commentable_type',
        'user_id',
        'parent_id',
        'approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
