<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPost extends Model
{
    use HasFactory;
    protected $table = 'chat_post';
    protected $fillable = [
        'content',
        'chat_id',
        'user_id'
    ];
}
