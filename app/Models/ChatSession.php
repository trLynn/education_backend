<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = ['user_id', 'sub_chapter_id', 'title'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
