<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'agent_id',
        'user_id',
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'status'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class);
    }
}
