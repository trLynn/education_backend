<?php
namespace App\Repositories\Eloquent;

use App\Models\Chapter;
use App\Repositories\Interfaces\ChapterRepositoryInterface;

class ChapterRepository implements ChapterRepositoryInterface {
    
    public function all() {
        // မိမိပိုင်ဆိုင်သော စာအုပ်များထဲမှ အခန်းများအားလုံးကို ဆွဲထုတ်ခြင်း
        return Chapter::whereHas('textbook', function($query) {
            $query->where('author_id', auth()->id());
        })->orderBy('order_index')->get();
    }

    public function create(array $data) {
        return Chapter::create($data);
    }

    public function findById($id) {
        // အခန်းငယ် (Sub-chapters) နှင့် သင်ခန်းစာ (Blocks) များပါ တစ်ခါတည်း ဆွဲထုတ်ခြင်း
        return Chapter::with('subChapters.blocks')->findOrFail($id);
    }

    public function update($id, array $data) {
        $record = Chapter::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id) {
        $record = Chapter::findOrFail($id);
        return $record->delete();
    }
}