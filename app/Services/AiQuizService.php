<?php

namespace App\Services;

use App\Models\SubChapter;
use App\Models\DailyAiUsage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Exception;

class AiQuizService
{
    /**
     * အသုံးပြုသူ၏ Quota ကို စစ်ဆေးပြီး မေးခွန်းထုတ်ပေးမည့် Main Function
     */
    public function checkQuotaAndGenerate($subChapterId)
    {
        $user = Auth::user();
        if (!$user) {
            throw new Exception("အကောင့်ဝင်ရန် လိုအပ်ပါသည်။");
        }

        $today = now()->toDateString(); // ဥပမာ - 2023-10-25
        $usage = null;

        // ၁။ Premium User မဟုတ်လျှင် Quota စစ်ဆေးမည်
        if (!$user->is_quiz_premium) {
            // ဒီနေ့အတွက် record ရှိမရှိရှာမည်၊ မရှိလျှင် count=0 ဖြင့် အသစ်ဆောက်မည်
            $usage = DailyAiUsage::firstOrCreate(
                ['user_id' => $user->id, 'usage_date' => $today],
                ['quiz_gen_count' => 0]
            );

            // ၅ ခေါက်နှင့်အထက် ဖြစ်နေလျှင် AI ဆီမပို့တော့ဘဲ Error ပြန်ပစ်မည်
            if ($usage->quiz_gen_count >= 5) {
                throw new Exception("QUOTA_EXCEEDED"); // Controller မှ ဖမ်းနိုင်ရန်
            }
        }

        // ၂။ Quota မပြည့်သေးလျှင် (သို့) Premium ဖြစ်နေလျှင် Groq AI သို့ လှမ်းခေါ်မည်
        $quizData = $this->callGroqApi($subChapterId);

        // ၃။ အောင်မြင်စွာ ထုတ်ပေးပြီးပါက Free User များအတွက် အကြိမ်အရေအတွက် (၁) တိုးပေးမည်
        if ($usage) {
            $usage->increment('quiz_gen_count');
        }

        return $quizData;
    }

    /**
     * Groq AI သို့ တိုက်ရိုက် လှမ်းခေါ်မည့် Private Function
     */
    private function callGroqApi($subChapterId)
    {
        // သင်ခန်းစာနှင့် ၎င်း၏ Blocks များကို ဆွဲထုတ်ခြင်း
        $subChapter = SubChapter::with('blocks')->findOrFail($subChapterId);

        // သင်ခန်းစာထဲက စာသားများကိုသာ ရွေးချယ်စုစည်းခြင်း (HTML tag များ ဖယ်ရှားပြီး)
        $content = $subChapter->blocks->whereIn('type', ['textbox', 'html'])
            ->map(fn($b) => strip_tags($b->content))
            ->implode("\n");

        if (empty(trim($content))) {
            throw new Exception("မေးခွန်းထုတ်ရန် သင်ခန်းစာ စာသားမရှိပါ။");
        }

        // AI သို့ ပို့မည့် အမိန့် (Prompt)
        // JSON Object ဖြင့် အတိအကျ ပြန်တောင်းထားသည်
        $prompt = "Based on the following text, generate 5 multiple-choice questions in JSON format. 
                   The JSON MUST have a root key 'questions' containing an array of objects.
                   Each object must have 'question', 'options' (as key-value pairs A, B, C, D), 'correct_answer' (just the key like 'A'), and 'explanation'.
                   Return ONLY the valid JSON.
                   Text: " . $content;

        // Groq API သို့ လှမ်းခေါ်ခြင်း (Guzzle လိုအပ်ပါသည်)
        $response = Http::withToken(env('GROQ_API_KEY'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_MODEL', 'llama3-8b-8192'), // Default အနေဖြင့် llama3 ကိုထားမည်
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert teacher and professional quiz generator. Respond ONLY in valid JSON format.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'response_format' => ['type' => 'json_object'], // JSON ကိုသာ တင်းကျပ်စွာ တောင်းဆိုခြင်း
                'temperature' => 0.5 // တိကျစေရန် (0 to 1)
            ]);

        // API ချိတ်ဆက်မှု ကျရှုံးခဲ့လျှင်
        if ($response->failed()) {
            throw new Exception("AI ဆာဗာနှင့် ချိတ်ဆက်၍ မရပါ။: " . $response->body());
        }

        // ရလာသော JSON String ကို PHP Array အဖြစ် ပြောင်းလဲခြင်း
        $responseBody = $response->json('choices.0.message.content');
        $decodedData = json_decode($responseBody, true);

        if (!$decodedData || !isset($decodedData['questions'])) {
            throw new Exception("AI မှ ပြန်ပို့သော ဒေတာပုံစံ မှားယွင်းနေပါသည်။");
        }

        return $decodedData;
    }
}
