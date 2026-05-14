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
        Schema::create('daily_ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date'); // ဥပမာ - 2023-10-25 (ဒီနေ့ရက်စွဲ)
            $table->integer('quiz_gen_count')->default(0); // ဘယ်နှခေါက် ထုတ်ပြီးပြီလဲ

            // User တစ်ယောက်အတွက် တစ်ရက်ကို Record တစ်ကြောင်းပဲ ရှိရမည်
            $table->unique(['user_id', 'usage_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_ai_usages');
    }
};
