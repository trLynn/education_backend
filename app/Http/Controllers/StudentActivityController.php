<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PurchaseService;
use App\Services\NoteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class StudentActivityController extends Controller {
    use ApiResponse;

    protected $purchaseService;
    protected $noteService;

    public function __construct(PurchaseService $purchaseService, NoteService $noteService) {
        $this->purchaseService = $purchaseService;
        $this->noteService = $noteService;
    }

    public function purchase(Request $request, $textbookId) {
        try {
            $purchase = $this->purchaseService->buyBook($textbookId);
            return $this->successResponse($purchase, "စာအုပ်ဝယ်ယူမှု အောင်မြင်ပါသည်။");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function storeNote(Request $request, $subChapterId) {
        try {
            $note = $this->noteService->saveNote($subChapterId, $request->content);
            return $this->successResponse($note, "မှတ်စု သိမ်းဆည်းပြီးပါပြီ။");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}