<?php

namespace App\Services;

use App\Models\ChatSession;
use Illuminate\Support\Facades\Http;
use Exception;

class AiTutorService
{
    public function askTutor(ChatSession $session, $newMessage, $contextText = null)
    {
        // ၁။ AI ကို အရင်ဆုံး System Persona (သင်ကြားရေးဆရာ) အဖြစ် သတ်မှတ်မည်
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful, expert AI Tutor for an interactive textbook. Explain concepts clearly, step-by-step, and in an encouraging tone. Answer in Burmese or English as requested. Do not give direct answers to homework or quizzes; instead, guide the student to find the answer.'
            ]
        ];

        // ၂။ ယခင်က မေးခဲ့ဖူးသော Chat History ကို Database မှ ဆွဲထုတ်ပြီး ထည့်မည် (နောက်ဆုံး မက်ဆေ့ခ်ျ ၁၀ ခု)
        $history = $session->messages()->latest()->take(10)->get()->reverse();
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->content
            ];
        }

        // ၃။ ကျောင်းသားက စာအုပ်ထဲက စာပိုဒ်ကို Highlight လုပ်ပြီး မေးခဲ့လျှင် ထိုစာပိုဒ်ကို Context အနေဖြင့် ထည့်ပေးမည်
        $userPrompt = $newMessage;
        if ($contextText) {
            $userPrompt = "Context from textbook: \"{$contextText}\"\n\nStudent's Question: {$newMessage}";
        }

        // ၄။ ယခုအသစ်မေးလိုက်သော မေးခွန်းကို ထည့်မည်
        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt
        ];

        // ၅။ Groq AI သို့ လှမ်းခေါ်မည် (Llama 3 70B ကို သုံးပါက ပို၍ ဉာဏ်ကောင်းပါသည်)
        $response = Http::withToken(env('GROQ_API_KEY'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => env('GROQ_TUTOR_MODEL', 'llama3-70b-8192'),
                'messages' => $messages,
                'temperature' => 0.7 // Creativity အနည်းငယ် ထည့်ရန်
            ]);

        if ($response->failed()) {
            throw new Exception("AI Tutor နှင့် ချိတ်ဆက်၍ မရပါ။");
        }

        // ၆။ AI ပြန်ဖြေလိုက်သော စာသားကို ယူမည်
        $aiResponseText = $response->json('choices.0.message.content');

        return $aiResponseText;
    }
}