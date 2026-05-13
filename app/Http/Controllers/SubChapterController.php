<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubChapterRequest;
use App\Http\Resources\SubChapterResource;
use App\Services\SubChapterService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SubChapterController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(SubChapterService $service)
    {
        $this->service = $service;
    }

    // ၁။ အခန်းငယ် (SubChapter) အသစ် ဖန်တီးရန်
    public function store(StoreSubChapterRequest $request)
    {
        try {
            $subChapter = $this->service->createSubChapter($request->validated());
            return $this->successResponse(new SubChapterResource($subChapter), "အခန်းငယ် ဖန်တီးမှု အောင်မြင်ပါသည်", 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }

    // ၂။ အခန်းငယ် အသေးစိတ်နှင့် ၎င်းအောက်ရှိ သင်ခန်းစာ (Blocks) များအားလုံးကို ကြည့်ရန်
    public function show($id)
    {
        try {
            // Service ထဲရှိ getSubChapter မှတဆင့် blocks များကိုပါ with('blocks') ဖြင့် ဆွဲထုတ်ပေးမည်
            $subChapter = $this->service->getSubChapter($id);
            return $this->successResponse(new SubChapterResource($subChapter), "အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse("အခန်းငယ် ရှာမတွေ့ပါ", 404);
        }
    }

    // ၃။ အခန်းငယ်၏ ခေါင်းစဉ်ကို ပြင်ဆင်ရန်
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            $subChapter = $this->service->updateSubChapter($id, $request->only('title'));
            return $this->successResponse(new SubChapterResource($subChapter), "ခေါင်းစဉ် ပြင်ဆင်မှု အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    // ၄။ အခန်းငယ်ကို ဖျက်သိမ်းရန် (Database Cascade ကြောင့် အောက်ရှိ Blocks များပါ ပျက်သွားမည်)
    public function destroy($id)
    {
        try {
            $this->service->deleteSubChapter($id);
            return $this->successResponse(null, "အခန်းငယ်ကို ဖျက်သိမ်းပြီးပါပြီ");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // ၅။ အခန်းငယ်များကို အပေါ်/အောက် အစီအစဉ် ပြောင်းလဲရန် (Drag & Drop အတွက်)
    public function reorder(Request $request)
    {
        $request->validate([
            'sub_chapters' => 'required|array',
            'sub_chapters.*.id' => 'required|integer|exists:sub_chapters,id',
            'sub_chapters.*.order_index' => 'required|integer'
        ]);

        try {
            $this->service->reorderSubChapters($request->sub_chapters);
            return $this->successResponse(null, "အစီအစဉ် ပြောင်းလဲမှု အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}