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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_chapter_id')->constrained()->cascadeOnDelete();

            // block အမျိုးအစား: textbox, equation, html, image_link, video_link
            $table->string('type');

            // Content ကို LongText ဖြင့် သိမ်းမည် (စာသား သို့မဟုတ် URL များ ထည့်ရန်)
            $table->longText('content')->nullable();

            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
