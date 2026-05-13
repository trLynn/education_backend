<?php
namespace App\Repositories\Eloquent;

use App\Models\StudentNote;
use App\Repositories\Interfaces\NoteRepositoryInterface;

class NoteRepository implements NoteRepositoryInterface {
    public function updateOrCreateNote($studentId, $subChapterId, $content) {
        return StudentNote::updateOrCreate(
            ['student_id' => $studentId, 'sub_chapter_id' => $subChapterId],
            ['content' => $content]
        );
    }

    public function getNote($studentId, $subChapterId) {
        return StudentNote::where('student_id', $studentId)
                          ->where('sub_chapter_id', $subChapterId)
                          ->first();
    }
}