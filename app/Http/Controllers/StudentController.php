<?php

namespace App\Http\Controllers;

use App\Services\NoteService;
use App\Services\PurchaseService;
use App\Services\TextbookService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    use ApiResponse;

    protected $purchaseService;
    protected $noteService;
    protected $textbookService;

    // Service များကို Controller ထဲသို့ ထည့်သွင်းခြင်း (Dependency Injection)
    public function __construct(
        PurchaseService $purchaseService,
        NoteService $noteService,
        TextbookService $textbookService
    ) {
        $this->purchaseService = $purchaseService;
        $this->noteService = $noteService;
        $this->textbookService = $textbookService;
    }

    // ၁။ စျေးကွက်ထဲရှိ ဝယ်ယူနိုင်သော စာအုပ်များ (Published) အားလုံးကို ပြသရန်
    public function availableBooks()
    {
        try {
            // TextbookService တွင် getPublishedBooks() ရေးပေးရန် လိုအပ်ပါသည်
            $books = $this->textbookService->getPublishedBooks();
            return $this->successResponse($books, "ရရှိနိုင်သော စာအုပ်များ");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // ၂။ ကျောင်းသားမှ စာအုပ်ကို ဝယ်ယူရန်
    public function purchase($textbookId)
    {
        try {
            $purchase = $this->purchaseService->buyBook($textbookId);
            return $this->successResponse($purchase, "စာအုပ်ဝယ်ယူမှု အောင်မြင်ပါသည်။", 201);
        } catch (\Exception $e) {
            // ဝယ်ပြီးသားဖြစ်နေလျှင် သို့မဟုတ် စာအုပ်မရှိလျှင် Error ပြမည်
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    // ၃။ ဝယ်ထားသော စာအုပ်ကို ဝင်ဖတ်ရန် (Content များနှင့် မိမိမှတ်စုများ ဆွဲထုတ်ရန်)
    public function readBook($textbookId)
    {
        try {
            // TextbookService တွင် readPurchasedBook() ရေးပေးရန် လိုအပ်ပါသည်
            $bookData = $this->textbookService->readPurchasedBook($textbookId, Auth::id());
            return $this->successResponse($bookData, "စာအုပ်အချက်အလက်များ ရရှိပါပြီ။");
        } catch (\Exception $e) {
            // မဝယ်ရသေးဘဲ ဝင်ဖတ်လျှင် Error ပြမည်
            return $this->errorResponse($e->getMessage(), 403);
        }
    }

    // ၄။ သင်ခန်းစာပေါ်တွင် ကိုယ်ပိုင်မှတ်စု ရေးသားသိမ်းဆည်းရန်
    public function storeNote(Request $request, $subChapterId)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            $note = $this->noteService->saveNote($subChapterId, $request->content);
            return $this->successResponse($note, "မှတ်စု သိမ်းဆည်းပြီးပါပြီ။");
        } catch (\Exception $e) {
            // စာအုပ်မဝယ်ရသေးဘဲ မှတ်စုလှမ်းရေးလျှင် Error ပြမည်
            return $this->errorResponse($e->getMessage(), 403);
        }
    }
}