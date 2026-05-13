<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreChapterRequest;
use App\Services\ChapterService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChapterController extends Controller {
    use ApiResponse;

    protected $service;

    public function __construct(ChapterService $service) {
        $this->service = $service;
    }

    public function store(StoreChapterRequest $request) {
        try {
            $chapter = $this->service->createChapter($request->validated());
            return $this->successResponse($chapter, "အခန်းအသစ် ဖန်တီးပြီးပါပြီ", 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show($id) {
        try {
            $chapter = $this->service->getChapter($id);
            return $this->successResponse($chapter, "အောင်မြင်ပါသည်");
        } catch (\Exception $e) {
            return $this->errorResponse("အခန်းရှာမတွေ့ပါ", 404);
        }
    }

    public function update(Request $request, $id) {
        $chapter = $this->service->updateChapter($id, $request->all());
        return $this->successResponse($chapter, "ပြင်ဆင်မှု အောင်မြင်ပါသည်");
    }

    public function destroy($id) {
        $this->service->deleteChapter($id);
        return $this->successResponse(null, "အခန်းကို ဖျက်သိမ်းပြီးပါပြီ");
    }
}