<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlockRequest;
use App\Http\Resources\BlockResource;
use App\Services\BlockService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(BlockService $service)
    {
        $this->service = $service;
    }

    // ၁။ Block အသစ်ဖန်တီးရန်
    public function store(StoreBlockRequest $request)
    {
        try {
            $block = $this->service->addBlock($request->validated());
            return $this->successResponse(new BlockResource($block), "သင်ခန်းစာ (Block) ဖန်တီးမှု အောင်မြင်ပါသည်", 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    // ၂။ Block အသေးစိတ် ကြည့်ရန် (ပုံမှန်အားဖြင့် SubChapter ကနေ တစ်ခါတည်းဆွဲထုတ်တတ်သော်လည်း လိုအပ်ပါက သုံးရန်)
    public function show($id)
    {
        // Service ထဲတွင် getBlock($id) ရေးထားရန် လိုအပ်သည်
        // $block = $this->service->getBlock($id);
        // return $this->successResponse(new BlockResource($block), "အောင်မြင်ပါသည်");
    }

    // ၃။ Block ၏ စာသား/အချက်အလက်များကို ပြင်ဆင်ရန်
    public function update(Request $request, $id)
    {
        // Update လုပ်ရာတွင် content သို့မဟုတ် type ပြောင်းလဲခြင်းကို လက်ခံသည်
        $request->validate([
            'type' => 'sometimes|string|in:textbox,equation,html,image_link,video_link',
            'content' => 'nullable|string',
        ]);

        try {
            $block = $this->service->updateBlock($id, $request->all());
            return $this->successResponse(new BlockResource($block), "ပြင်ဆင်မှု အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    // ၄။ Block ကို ဖျက်ရန်
    public function destroy($id)
    {
        try {
            $this->service->deleteBlock($id);
            return $this->successResponse(null, "ဖျက်သိမ်းပြီးပါပြီ");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // ၅။ Block များကို အပေါ်/အောက် အစီအစဉ် ပြောင်းလဲရန် (Drag & Drop အတွက်)
    public function reorder(Request $request)
    {
        // Frontend မှ Array ပုံစံဖြင့် [{'id': 1, 'order_index': 1}, ...] ပို့ပေးရမည်
        $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'required|integer|exists:blocks,id',
            'blocks.*.order_index' => 'required|integer'
        ]);

        try {
            // BlockService ထဲတွင် updateOrder ကို ခေါ်သုံးမည်
            $this->service->reorderBlocks($request->blocks);
            return $this->successResponse(null, "အစီအစဉ် ပြောင်းလဲမှု အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}