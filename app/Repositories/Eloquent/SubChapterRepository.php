<?php

namespace App\Repositories\Eloquent;

use App\Models\SubChapter;
use App\Repositories\Interfaces\SubChapterRepositoryInterface;

class SubChapterRepository implements SubChapterRepositoryInterface
{
    public function create(array $data)
    {
        return SubChapter::create($data);
    }

    public function findById($id)
    {
        return SubChapter::with('blocks')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $record = SubChapter::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = SubChapter::findOrFail($id);
        return $record->delete();
    }
}
