<?php

namespace App\Services;

use App\Repositories\Interfaces\BlockRepositoryInterface;
use App\Repositories\Interfaces\SubChapterRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier; // HTML သန့်စင်ရန် ထည့်သွင်းထားသည်
use Exception;

class BlockService
{
    protected $repository;
    protected $subChapterRepo;

    // SubChapterRepository ကိုပါ Dependency Injection ဖြင့် ထည့်သွင်းလိုက်ပါသည်
    public function __construct(
        BlockRepositoryInterface $repository,
        SubChapterRepositoryInterface $subChapterRepo
    ) {
        $this->repository = $repository;
        $this->subChapterRepo = $subChapterRepo;
    }

    public function addBlock($data)
    {
        // Model အစား Repository ကို အသုံးပြု၍ ရှာဖွေခြင်း (Clean Architecture)
        $subChapter = $this->subChapterRepo->findById($data['sub_chapter_id']);

        // 1. Authorization check
        if ($subChapter->chapter->textbook->author_id !== Auth::id()) {
            throw new Exception("ဤနေရာတွင် သင်ခန်းစာဖန်တီးခွင့် မရှိပါ။ (Access Denied)");
        }

        // 2. Type Validation Logic
        $allowedTypes = ['textbox', 'equation', 'html', 'image_link', 'video_link'];
        if (!in_array($data['type'], $allowedTypes)) {
            throw new Exception("Block အမျိုးအစား မှားယွင်းနေပါသည်။ (Invalid block type)");
        }

        // 3. HTML Sanitization (အန္တရာယ်ရှိသော Script များ ဖယ်ရှားခြင်း) 🔥
        if ($data['type'] === 'html' && isset($data['content'])) {
            $data['content'] = Purifier::clean($data['content'], 'textbook_editor');
        }

        $data['order_index'] = $subChapter->blocks()->count() + 1;
        return $this->repository->create($data);
    }

    public function updateBlock($id, $data)
    {
        // Update လုပ်ရာတွင်လည်း Content ပါလာလျှင် သန့်စင်ပေးရမည်
        if (isset($data['type']) && $data['type'] === 'html' && isset($data['content'])) {
            // Type ပါလာပြီး html ဖြစ်နေလျှင်
            $data['content'] = Purifier::clean($data['content'], 'textbook_editor');
        } elseif (!isset($data['type']) && isset($data['content'])) {
            // Type မပါဘဲ Content သီးသန့် Update လာလုပ်လျှင်လည်း လုံခြုံရေးအရ သန့်စင်ထားမည်
            $data['content'] = Purifier::clean($data['content'], 'textbook_editor');
        }

        return $this->repository->update($id, $data);
    }

    public function deleteBlock($id)
    {
        return $this->repository->delete($id);
    }

    // အစီအစဉ် ပြောင်းလဲရန် Logic (Drag & Drop)
    public function reorderBlocks(array $blocksData)
    {
        return $this->repository->updateOrder($blocksData);
    }
}