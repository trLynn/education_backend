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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_chapter_id')->nullable()->constrained()->nullOnDelete(); // ဘယ်သင်ခန်းစာမှာ မေးနေတာလဲ သိရန်
            $table->string('title')->nullable(); // Chat ခေါင်းစဉ် (ဥပမာ - "Physics Chapter 1 မေးခွန်းများ")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
