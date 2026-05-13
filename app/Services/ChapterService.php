<?php
namespace App\Services;

use App\Models\Chapter;
use App\Models\Textbook;
use App\Repositories\Interfaces\ChapterRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ChapterService {
    protected $repository;

    public function __construct(ChapterRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function createChapter($data) {
        // ၁။ Textbook ပိုင်ရှင် ဟုတ်၊ မဟုတ် စစ်ဆေးခြင်း
        $textbook = Textbook::findOrFail($data['textbook_id']);
        if ($textbook->author_id !== Auth::id()) {
            throw new \Exception("ဤစာအုပ်တွင် အခန်းဖန်တီးခွင့် မရှိပါ။");
        }

        // ၂။ Order Index ကို အလိုအလျောက် တွက်ချက်ခြင်း (optional)
        // ရှိပြီးသား အခန်းအရေအတွက်ကို ကြည့်ပြီး နောက်ဆုံးမှာ ထည့်ပေးမည်
        $data['order_index'] = Chapter::where('textbook_id', $data['textbook_id'])->count() + 1;

        return $this->repository->create($data);
    }

    public function getChapter($id) {
        return $this->repository->findById($id);
    }

    public function updateChapter($id, $data) {
        return $this->repository->update($id, $data);
    }

    public function deleteChapter($id) {
        return $this->repository->delete($id);
    }
}