<?php

namespace App\Models;

use App\Models\Question;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['sub_chapter_id', 'title'];

    // SubChapter နှင့် ချိတ်ဆက်ခြင်း
    public function subChapter()
    {
        return $this->belongsTo(SubChapter::class);
    }

    // ဤ Quiz အောက်ရှိ မေးခွန်းများနှင့် ချိတ်ဆက်ခြင်း
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}