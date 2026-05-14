<?php

namespace App\Http\Controllers;

use App\Services\AiQuizService;
use App\Models\Quiz;
use App\Models\SubChapter;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Exception;

class QuizController extends Controller
{
    use ApiResponse; // အရင်က ကျွန်တော်တို့ သုံးခဲ့တဲ့ Response အလှဆင်သည့် Trait

    protected $aiService;

    // Dependency Injection ဖြင့် Service ကို လှမ်းခေါ်ခြင်း
    public function __construct(AiQuizService $aiService)
    {
        $this->aiService = $aiService;
    }

    // AI ဖြင့် မေးခွန်းထုတ်ပြီး သိမ်းမည့် Function
    public function generateAiQuiz(Request $request, $subChapterId)
    {
        try {
            // ၁။ Service မှတဆင့် Groq AI ဆီသို့ လှမ်းခေါ်၍ JSON Data တောင်းခံခြင်း
            $quizData = $this->aiService->checkQuotaAndGenerate($subChapterId);
            
            $subChapter = SubChapter::findOrFail($subChapterId);

            // ၂။ Quiz ခေါင်းစဉ် (Title) ကို Database တွင် အရင်သိမ်းခြင်း
            $quiz = Quiz::create([
                'sub_chapter_id' => $subChapterId,
                'title' => "AI Generated Quiz for: " . $subChapter->title
            ]);

            // ၃။ AI မှ ပြန်ရလာသော မေးခွန်းများကို Questions Table ထဲသို့ Loop ပတ်၍ သိမ်းခြင်း
            foreach ($quizData['questions'] as $q) {
                $quiz->questions()->create([
                    'question_text' => $q['question'],
                    'options' => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                    // Explanation ပါလာလျှင်ထည့်မည်၊ မပါလာလျှင် null ထားမည်
                    'explanation' => $q['explanation'] ?? null 
                ]);
            }

            // ၄။ အောင်မြင်ကြောင်းနှင့် သိမ်းပြီးသော Data ကို ပြန်ပြခြင်း
            return $this->successResponse(
                $quiz->load('questions'), 
                "AI ဖြင့် မေးခွန်းများ အောင်မြင်စွာ ဖန်တီးသိမ်းဆည်းပြီးပါပြီ။", 
                201
            );

        } catch (Exception $e) {
            // Error တစ်ခုခုတက်ခဲ့လျှင် (ဥပမာ - API Limit ပြည့်သွားခြင်း)
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}