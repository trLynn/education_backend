<?php
namespace App\Services;

use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Interfaces\PurchaseRepositoryInterface;
use App\Repositories\Interfaces\SubChapterRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class NoteService {
    protected $noteRepo;
    protected $purchaseRepo;
    protected $subChapterRepo;

    // Repository (၃) ခုလုံးကို Dependency Injection ဖြင့် ထည့်သွင်းခြင်း
    public function __construct(
        NoteRepositoryInterface $noteRepo,
        PurchaseRepositoryInterface $purchaseRepo,
        SubChapterRepositoryInterface $subChapterRepo
    ) {
        $this->noteRepo = $noteRepo;
        $this->purchaseRepo = $purchaseRepo;
        $this->subChapterRepo = $subChapterRepo;
    }

    public function saveNote($subChapterId, $content) {
        // ၁။ Login ဝင်ထားသော User ကို စနစ်တကျ ဆွဲထုတ်ခြင်း (Error မတက်စေရန်)
        $user = Auth::user();
        
        if (!$user) {
            throw new Exception("မှတ်စုရေးရန် အကောင့်ဝင်ဖို့ လိုအပ်ပါသည်။");
        }

        $studentId = $user->id;

        // ၂။ Model ကို တိုက်ရိုက်မခေါ်ဘဲ Repository မှတစ်ဆင့် SubChapter ကို ရှာဖွေခြင်း
        $subChapter = $this->subChapterRepo->findById($subChapterId);
        
        // SubChapter မှတစ်ဆင့် ၎င်းပါဝင်သော Textbook ၏ ID ကို ယူခြင်း
        $textbookId = $subChapter->chapter->textbook_id;

        // ၃။ ကျောင်းသားသည် ဤစာအုပ်ကို ဝယ်ထားခြင်း ရှိ/မရှိ စစ်ဆေးခြင်း
        $purchase = $this->purchaseRepo->findPurchase($studentId, $textbookId);
        
        // ၄။ ဝယ်မထားလျှင် သို့မဟုတ် Root အကောင့် မဟုတ်လျှင် ခွင့်မပြုပါ
        if (!$purchase && $user->role !== 'root') {
            throw new Exception("မှတ်စုရေးရန် ဤစာအုပ်ကို အရင်ဝယ်ယူရန် လိုအပ်ပါသည်။");
        }

        // ၅။ အားလုံးမှန်ကန်ပါက မှတ်စုကို သိမ်းဆည်းမည် (ရှိပြီးသားဆိုလျှင် Update လုပ်မည်)
        return $this->noteRepo->updateOrCreateNote($studentId, $subChapterId, $content);
    }
}