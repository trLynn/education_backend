<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubChapterController;
use App\Http\Controllers\TextbookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login ဝင်ပြီးသား User မှသာ အသုံးပြုနိုင်မည့် Routes များ
Route::middleware('auth:sanctum')->group(function () {
    
    // ==========================================
    // ဆရာ (Teacher / Admin) များအတွက် CRUD Routes
    // ==========================================
    
    Route::apiResource('textbooks', TextbookController::class);
    Route::apiResource('chapters', ChapterController::class);
    Route::apiResource('sub-chapters', SubChapterController::class);
    Route::apiResource('blocks', BlockController::class);

    // အစီအစဉ် ရွှေ့ရန် (Drag & Drop) အတွက် Custom Routes
    Route::post('sub-chapters/reorder', [SubChapterController::class, 'reorder']);
    Route::post('blocks/reorder', [BlockController::class, 'reorder']);


    // ==========================================
    // ကျောင်းသား (Student) များအတွက် Routes
    // ==========================================
    
    // ၁။ စျေးကွက်ထဲရှိ ဝယ်ယူနိုင်သော စာအုပ်များကို ကြည့်ရန်
    Route::get('student/available-books', [StudentController::class, 'availableBooks']);
    
    // ၂။ စာအုပ်ဝယ်ယူရန်
    Route::post('student/textbooks/{id}/purchase', [StudentController::class, 'purchase']);
    
    // ၃။ ဝယ်ထားသော စာအုပ်ကို ဝင်ဖတ်ရန် (မှတ်စုများနှင့်အတူ)
    Route::get('student/textbooks/{id}/read', [StudentController::class, 'readBook']);
    
    // ၄။ သင်ခန်းစာပေါ်တွင် ကိုယ်ပိုင်မှတ်စု ရေးသားရန်
    Route::post('student/sub-chapters/{id}/notes', [StudentController::class, 'storeNote']);
    
    // ==========================================
    // လက်ရှိ User အချက်အလက် ကြည့်ရန် (Optional)
    // ==========================================
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('sub-chapters/{id}/generate-quiz', [QuizController::class, 'generateAiQuiz']);

    // AI Tutor နှင့် ချိတ်ဆက်ရန် Routes များ
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{sessionId}', [ChatController::class, 'getChatHistory']);
});