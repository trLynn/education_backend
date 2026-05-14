<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'quiz_id', 
        'question_text', 
        'options', 
        'correct_answer', 
        'explanation'
    ];

    // Database ထဲတွင် JSON အနေဖြင့် သိမ်းထားသော်လည်း 
    // Laravel ထဲရောက်လျှင် Array အဖြစ် အလိုအလျောက် ပြောင်းပေးရန် Cast လုပ်ခြင်း 🔥
    protected $casts = [
        'options' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}