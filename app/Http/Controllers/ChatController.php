<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Services\AiTutorService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponse;

    protected $tutorService;

    public function __construct(AiTutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    // မက်ဆေ့ခ်ျ ပေးပို့ရန် (Session အသစ်ဖန်တီးခြင်း သို့မဟုတ် ရှိပြီးသားတွင် ဆက်ပြောခြင်း)
    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_session_id' => 'nullable|exists:chat_sessions,id',
            'sub_chapter_id' => 'nullable|exists:sub_chapters,id',
            'message' => 'required|string',
            'highlighted_text' => 'nullable|string' // စာအုပ်ထဲက highlight လုပ်ထားတဲ့ စာသား
        ]);

        $user = Auth::user();

        // ၁။ Chat Session ရှာမည် (သို့မဟုတ်) အသစ်ဆောက်မည်
        $session = ChatSession::find($request->chat_session_id);
        if (!$session) {
            $session = ChatSession::create([
                'user_id' => $user->id,
                'sub_chapter_id' => $request->sub_chapter_id,
                'title' => substr($request->message, 0, 30) . "..." // မေးခွန်းအစကို ခေါင်းစဉ်ပေးမည်
            ]);
        }

        // ၂။ ကျောင်းသား မေးလိုက်သောစာကို Database တွင် သိမ်းမည်
        $session->messages()->create([
            'role' => 'user',
            'content' => $request->message
        ]);

        try {
            // ၃။ Service မှတဆင့် AI သို့ လှမ်းမေးမည်
            $aiResponse = $this->tutorService->askTutor(
                $session, 
                $request->message, 
                $request->highlighted_text
            );

            // ၄။ AI ပြန်ဖြေလိုက်သောစာကို Database တွင် သိမ်းမည်
            $assistantMessage = $session->messages()->create([
                'role' => 'assistant',
                'content' => $aiResponse
            ]);

            return response()->json([
                'success' => true,
                'chat_session_id' => $session->id,
                'response' => $assistantMessage
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ယခင် Chat History များကို ပြန်ကြည့်ရန်
    public function getChatHistory($sessionId)
    {
        $session = ChatSession::where('user_id', Auth::id())
                              ->with('messages')
                              ->findOrFail($sessionId);

        return $this->successResponse($session, "Chat history ရရှိပါပြီ");
    }
}