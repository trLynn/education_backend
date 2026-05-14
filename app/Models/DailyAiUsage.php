<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAiUsage extends Model
{
    protected $fillable = ['user_id', 'usage_date', 'quiz_gen_count'];
}
