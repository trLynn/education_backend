<?php
namespace App\Services;

use App\Repositories\Interfaces\TextbookRepositoryInterface;
use App\Repositories\Interfaces\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TextbookService {
    protected $textbookRepo;
    protected $purchaseRepo;

    // Repository များကို Dependency Injection ဖြင့် ထည့်သွင်းခြင်း
    public function __construct(
        TextbookRepositoryInterface $textbookRepo,
        PurchaseRepositoryInterface $purchaseRepo
    ) {
        $this->textbookRepo = $textbookRepo;
        $this->purchaseRepo = $purchaseRepo;
    }

    // ==========================================
    // ဆရာ (Teacher) များအတွက် လုပ်ဆောင်ချက်များ
    // ==========================================

    public function getAllBooks() {
        return $this->textbookRepo->all();
    }

    public function createBook($data) {
        $user = Auth::user();

        // ၁။ Role Check
        if ($user->role === 'student') {
            throw new \Exception("ကျောင်းသားများသည် စာအုပ်ဖန်တီးခွင့် မရှိပါ။");
        }

        // ၂။ Limit Check
        if ($this->textbookRepo->countByAuthor($user->id) >= 5 && $user->role !== 'root') {
            throw new \Exception("သင့်အနေဖြင့် စာအုပ် ၅ အုပ်အထိသာ ဖန်တီးခွင့် ရှိပါသည်။");
        }

        // ၃။ Title Unique Check
        if ($this->textbookRepo->findByTitle($data['title'])) {
            throw new \Exception("ဤစာအုပ်အမည်မှာ ရှိနှင့်ပြီးသား ဖြစ်ပါသည်။");
        }

        return DB::transaction(function () use ($data, $user) {
            $data['author_id'] = $user->id;
            return $this->textbookRepo->create($data);
        });
    }

    public function getBook($id) {
        return $this->textbookRepo->findById($id);
    }

    public function updateBook($id, $data) {
        return $this->textbookRepo->update($id, $data);
    }

    public function deleteBook($id) {
        return $this->textbookRepo->delete($id);
    }


    // ==========================================
    // ကျောင်းသား (Student) များအတွက် လုပ်ဆောင်ချက်များ
    // ==========================================

    // ၁။ စျေးကွက်ထဲရှိ Public စာအုပ်များအားလုံး ကြည့်ရန်
    public function getPublishedBooks() {
        return $this->textbookRepo->getPublished();
    }

    // ၂။ ဝယ်ထားသော စာအုပ်ကို မှတ်စုများနှင့်တကွ ဖတ်ရန်
    public function readPurchasedBook($textbookId, $studentId) {
        // စာအုပ်ဝယ်ထားခြင်း ရှိ/မရှိ ကို PurchaseRepository မှတစ်ဆင့် စစ်ဆေးခြင်း
        $purchase = $this->purchaseRepo->findPurchase($studentId, $textbookId);
                                        
        if (!$purchase && Auth::user()->role !== 'root') {
            throw new \Exception("ဤစာအုပ်ကို ဖတ်ရှုရန် အရင်ဝယ်ယူဖို့ လိုအပ်ပါသည်။");
        }

        // ဝယ်ထားသည်မှာ သေချာပါက Repository ကိုသုံး၍ ဒေတာများ ဆွဲထုတ်ခြင်း
        return $this->textbookRepo->findWithStudentNotes($textbookId, $studentId);
    }
}