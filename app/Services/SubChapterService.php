<?php

namespace App\Services;

use App\Models\Chapter;
use App\Repositories\Interfaces\SubChapterRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SubChapterService
{
    protected $repository;

    public function __construct(SubChapterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createSubChapter($data)
    {
        // Parent Chapter ကို လက်ရှိဆရာ ပိုင်မပိုင် စစ်ဆေးခြင်း
        $chapter = Chapter::findOrFail($data['chapter_id']);
        if ($chapter->textbook->author_id !== Auth::id()) {
            throw new \Exception("ဤအခန်းတွင် အခန်းငယ်ဖန်တီးခွင့် မရှိပါ။");
        }

        $data['order_index'] = $chapter->subChapters()->count() + 1;
        return $this->repository->create($data);
    }

    public function updateSubChapter($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSubChapter($id)
    {
        return $this->repository->delete($id);
    }

    // SubChapter အသေးစိတ်ကို ရှာရန်
    public function getSubChapter($id)
    {
        return $this->repository->findById($id);
    }

    // Drag and Drop အစီအစဉ် ပြောင်းရန်
    public function reorderSubChapters(array $subChaptersData)
    {
        // သတိပြုရန်: SubChapterRepository ထဲတွင် Block ကဲ့သို့ updateOrder method ရေးပေးထားရန် လိုအပ်ပါသည်
        return $this->repository->updateOrder($subChaptersData);
    }
}
