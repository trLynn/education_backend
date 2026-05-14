<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->json('options'); // ['A' => '...', 'B' => '...'] စသဖြင့် သိမ်းရန်
            $table->string('correct_answer'); // 'A' သို့မဟုတ် 'B'
            $table->text('explanation')->nullable(); // အဖြေရှင်းလင်းချက် (AI ကို ထုတ်ခိုင်းမည်)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
