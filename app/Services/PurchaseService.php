<?php
namespace App\Services;

use App\Models\Textbook;
use App\Repositories\Interfaces\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PurchaseService {
    protected $repository;

    public function __construct(PurchaseRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function buyBook($textbookId) {
        $studentId = Auth::id();

        // ၁။ စာအုပ်ရှိမရှိနှင့် Published ဖြစ်မဖြစ်စစ်ဆေးခြင်း
        $textbook = Textbook::findOrFail($textbookId);
        if (!$textbook->is_published) {
            throw new \Exception("ဤစာအုပ်မှာ လက်ရှိဝယ်ယူ၍မရသေးပါ။");
        }

        // ၂။ ဝယ်ပြီးသားဖြစ်နေသလား စစ်ဆေးခြင်း
        if ($this->repository->findPurchase($studentId, $textbookId)) {
            throw new \Exception("သင်သည် ဤစာအုပ်ကို ဝယ်ယူပြီးဖြစ်ပါသည်။");
        }

        return $this->repository->create([
            'student_id' => $studentId,
            'textbook_id' => $textbookId,
            'purchased_at' => now()
        ]);
    }
}